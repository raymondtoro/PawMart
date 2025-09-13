<?php

namespace App\Http\Controllers\User;

use App\Models\Cart;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\PesananProduk;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    /** ---------------- Helper: Calculate totals ---------------- */
    private function calculateTotals($items)
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $discount = $subtotal > 200000 ? 20000 : 0;
        $shipping = $subtotal > 100000 ? 0 : 10000;
        $total = $subtotal - $discount + $shipping;

        return compact('subtotal', 'discount', 'shipping', 'total');
    }

    /** ---------------- Show transaction page (from cart) ---------------- */
    public function index(Request $request)
    {
        if (empty(auth()->user()->alamat)) {
            return redirect()->back()->with('warning', 'Anda belum mengisi alamat. Silakan lengkapi alamat sebelum melanjutkan ke checkout.');
        }

        $selectedRaw = $request->input('produk_id', []);
        $selected = is_array($selectedRaw) ? Arr::flatten($selectedRaw) : [$selectedRaw];
        $selected = array_map('intval', $selected);

        if (empty($selected)) {
            return redirect()->route('cart')->with('error', 'Pilih produk terlebih dahulu.');
        }

        $qtyRaw = $request->input('qty', []);
        $quantities = is_array($qtyRaw) ? Arr::flatten($qtyRaw) : [];

        // Fetch cart items with the correct relationship
        $orders = Cart::with('product.promotion')
            ->where('user_id', auth()->id())
            ->whereIn('id', $selected)
            ->get()
            ->keyBy('id');

        $finalOrders = collect();

        foreach ($selected as $index => $id) {
            if ($orders->has($id)) {
                $finalOrders->push($orders->get($id));
            } else {
                $product = \App\Models\Product::with('promotion')->find($id);
                if ($product) {
                    $finalOrders->push((object)[
                        'id' => $product->id,
                        'quantity' => $quantities[$index] ?? 1,
                        'product' => $product
                    ]);
                }
            }
        }

        // Prepare items for totals with correct promotion
        $items = $finalOrders->map(function ($o) {
            $promo = $o->product->promotion;
            $diskon = $promo->diskon ?? 0;
            $harga = $o->product->harga_produk;
            $hargaFinal = $diskon > 0 ? round($harga * (1 - $diskon / 100)) : $harga;

            return [
                'id'    => $o->id,
                'price' => $hargaFinal,
                'qty'   => $o->quantity,
                'cart'  => $o
            ];
        })->toArray();

        $totals = $this->calculateTotals($items);

        $selectedJson = json_encode(array_map(fn($i) => $i['id'], $items));
        $qtyJson      = json_encode(array_map(fn($i) => $i['qty'], $items));

        return view('user.transaction', [
            'orders'       => $finalOrders,
            'subtotal'     => $totals['subtotal'],
            'discount'     => $totals['discount'],
            'shipping'     => $totals['shipping'],
            'total'        => $totals['total'],
            'selectedJson' => $selectedJson,
            'qtyJson'      => $qtyJson,
        ]);
    }

    /** ---------------- Buy single product directly from details page ---------------- */
    public function buy(Request $request)
    {
        $productId = $request->input('produk_id');
        $quantity = $request->input('qty', 1);

        return redirect()->route('user.transaction', [
            'produk_id' => [$productId],
            'qty'       => [$quantity]
        ]);
    }

    /** ---------------- Finalize transaction ---------------- */
    public function finalize(Request $request)
    {
        $request->validate([
            'alamat_pengiriman' => 'required|string|max:255',
            'metode_transaksi'  => 'required|string',
            'products'          => 'required|string',
            'quantities'        => 'required|string',
        ]);

        try {
            $cartIds = json_decode($request->input('products'), true);
            $qtys    = json_decode($request->input('quantities'), true);

            $cartIds = array_map('intval', Arr::flatten($cartIds));
            $qtys    = array_map('intval', Arr::flatten($qtys));

            if (empty($cartIds) || empty($qtys) || count($cartIds) !== count($qtys)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk atau jumlah tidak valid.'
                ], 422);
            }

            $cartItems = collect();

            $existingCartItems = Cart::with('product.promotion')
                ->where('user_id', auth()->id())
                ->whereIn('id', $cartIds)
                ->get()
                ->keyBy('id');

            foreach ($cartIds as $index => $id) {
                if ($existingCartItems->has($id)) {
                    $item = $existingCartItems->get($id);
                    $item->quantity = $qtys[$index];
                    $cartItems->push($item);
                } else {
                    $product = \App\Models\Product::with('promotion')->find($id);
                    if ($product) {
                        $cartItems->push((object)[
                            'id'       => $product->id,
                            'quantity' => $qtys[$index],
                            'product'  => $product,
                        ]);
                    }
                }
            }

            // Prepare items for order
            $items = $cartItems->map(function ($item) {
                $promo = $item->product->promotion;
                $diskon = $promo->diskon ?? 0;
                $harga = $item->product->harga_produk;
                $hargaFinal = $diskon > 0 ? round($harga * (1 - $diskon / 100)) : $harga;

                return [
                    'produk_id' => $item->product->id,
                    'price'     => $hargaFinal,
                    'qty'       => $item->quantity,
                ];
            })->toArray();

            $totals = $this->calculateTotals($items);

            $order = Order::create([
                'user_id'           => auth()->id(),
                'tanggal_pesanan'   => now(),
                'total_pesanan'     => $totals['total'],
                'status_pesanan'    => 'pending',
                'alamat_pengiriman' => auth()->user()->alamat,
                'catatan'           => $request->input('catatan'),
            ]);

            foreach ($items as $item) {
                PesananProduk::create([
                    'pesanan_id'      => $order->id,
                    'produk_id'       => $item['produk_id'],
                    'jumlah'          => $item['qty'],
                    'harga_saat_beli' => $item['price'],
                ]);
            }

            $transaction = Transaction::create([
                'pesanan_id'        => $order->id,
                'tanggal_transaksi' => now(),
                'total_transaksi'   => $totals['total'],
                'status_transaksi'  => 'pending',
                'metode_transaksi'  => $request->input('metode_transaksi'),
                'ongkir'            => $totals['shipping'],
            ]);

            Cart::where('user_id', auth()->id())
                ->whereIn('id', $existingCartItems->keys())
                ->delete();

            return response()->json([
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat!',
                'redirect_url' => route('user.transaction.show', $transaction->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** ---------------- Show transaction detail ---------------- */
    public function show($id)
    {
        $transaction = Transaction::with(['order.orderItems.produk'])
            ->where('id', $id)
            ->whereHas('order', fn($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();

        return view('user.show', compact('transaction'));
    }

    public function invoice($id)
    {
        $transaction = Transaction::with(['order.orderItems.produk'])
            ->where('id', $id)
            ->whereHas('order', fn($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();

        $pdf = Pdf::loadView('user.invoice', compact('transaction'));
        return $pdf->download('invoice-' . $transaction->id . '.pdf');
    }
}
