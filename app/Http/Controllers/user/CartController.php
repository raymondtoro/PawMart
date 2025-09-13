<?php

namespace App\Http\Controllers\User;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Show cart page (blade) — Eloquent models with relation
    public function index()
{
    $cartItems = Cart::with('product')
        ->where('user_id', Auth::id())
        ->get();

    // ✅ Calculate subtotal, shipping, discount, total
    $subtotal = $cartItems->sum(function ($item) {
        return $item->product->harga_produk * $item->quantity;
    });

    $shipping = $subtotal > 100000 ? 0 : 10000; // contoh aturan ongkir
    $discount = 0; // ganti kalau ada promo logic
    $total = $subtotal + $shipping - $discount;

    return view('user.cart', compact(
        'cartItems',
        'subtotal',
        'shipping',
        'discount',
        'total'
    ));
}

    // JSON endpoint for frontend JS to fetch cart (returns structured JSON)
    public function data()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        // map into a safe JSON format
        $payload = $cartItems->map(function ($c) {
            return [
                'cart_id' => $c->id,
                'produk_id' => $c->produk_id,
                'quantity' => $c->quantity,
                'price' => $c->price,
                'product' => $c->product ? [
                    'id' => $c->product->id,
                    'nama_produk' => $c->product->nama_produk,
                    'harga_produk' => $c->product->harga_produk,
                    'diskon' => $c->product->diskon, 
                    'gambar_produk' => $c->product->gambar_produk,
                ] : null,
            ];
        });

        return response()->json($payload);
    }

    public function checkout(Request $request)
{
    $cartIds = $request->input('cart_ids', []);

    if (empty($cartIds)) {
        return response()->json(['error' => 'Tidak ada produk yang dipilih'], 400);
    }

    // Ambil item keranjang yang dipilih
    $items = Cart::with('product')
        ->whereIn('id', $cartIds)
        ->where('user_id', auth()->id())
        ->get();

    if ($items->isEmpty()) {
        return response()->json(['error' => 'Keranjang tidak ditemukan'], 404);
    }

    // Simpan ke session biar bisa ditampilkan di halaman transaksi
    session(['checkout_items' => $items]);

    return response()->json(['success' => true]);
}


    // Add item to cart (AJAX or form). Expects 'produk_id'.
    public function add(Request $request)
    {
        $productId = $request->input('produk_id') ?? $request->input('product_id');
        $product = Product::findOrFail($productId);

        $cartItem = Cart::firstOrCreate(
            ['user_id' => Auth::id(), 'produk_id' => $productId],
            ['quantity' => 0, 'price' => $product->harga_produk]
        );

        $cartItem->quantity += 1;
        $cartItem->price = $product->harga_produk;
        $cartItem->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => "{$product->nama_produk} berhasil ditambahkan ke keranjang",
                'produk_id' => $productId,
            ]);
        }

        return redirect()->route('user.cart')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    // Update quantity (AJAX). Expects produk_id and quantity.
    // Update quantity (AJAX). Expects cart_id and quantity.
public function update(Request $request)
{
    $cartId = $request->input('cart_id');
    $quantity = (int) $request->input('quantity', 1);

    $cartItem = Cart::where('user_id', Auth::id())
        ->where('id', $cartId)
        ->first();

    if (!$cartItem) {
        return response()->json(['status' => 'error', 'message' => 'Item tidak ditemukan'], 404);
    }

    $cartItem->quantity = max(1, $quantity);
    $cartItem->save();

    return response()->json([
        'status' => 'ok',
        'quantity' => $cartItem->quantity,
        'subtotal' => $cartItem->product->harga_produk * $cartItem->quantity
    ]);
}

// Remove by cart_id (form or AJAX)
public function remove(Request $request)
{
    $cartId = $request->input('cart_id');

    $deleted = Cart::where('user_id', Auth::id())
        ->where('id', $cartId)
        ->delete();

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['status' => $deleted ? 'ok' : 'error']);
    }

    return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
}


    // Clear all for user
    public function clear(Request $request)
    {
        Cart::where('user_id', Auth::id())->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}
