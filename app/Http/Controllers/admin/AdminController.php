<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Rating;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        // Summary cards
        $totalOrders = Order::count();
        $outOfStock = Product::where('stok_produk', '<=', 0)->count();
        $totalRevenue = Transaction::sum('total_transaksi');
        $avgRating = Rating::avg('bintang');

        // Latest orders (limit 5)
        $latestOrders = Order::with('user', 'produk')
            ->latest()
            ->take(5)
            ->get();

        // Inventory report (grouped by kategori)
    $inventory = DB::table('produk')
        ->join('kategori', 'produk.kategori_id', '=', 'kategori.id')
        ->select('kategori.nama_kategori', DB::raw('SUM(produk.stok_produk) as total_stok'))
        ->groupBy('kategori.nama_kategori')
        ->orderByDesc('total_stok')
        ->take(5)
        ->get();

    // Find max stok for scaling
    $maxStock = $inventory->max('total_stok');

    return view('admin.dashboard', compact(
        'totalOrders',
        'outOfStock',
        'totalRevenue',
        'avgRating',
        'latestOrders',
        'inventory',
        'maxStock'
        ));
    }
}
