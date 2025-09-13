<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    public function index(Request $request)
{
    // Get all categories
    $categories = Category::all();

    // Capture search keyword and selected category from request
    $search = $request->q;
    $selectedKategori = $request->kategori;

    // Start query builder
    $productsQuery = Product::with('category');

    // Filter by search keyword (name or category)
    if ($search) {
        $productsQuery->where(function ($query) use ($search) {
            $query->where('nama_produk', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('nama_kategori', 'like', "%{$search}%");
                  });
        });
    }

    // Filter by selected category (from sidebar click)
    if ($selectedKategori) {
        $productsQuery->where('kategori_id', $selectedKategori);
    }

    // Execute query
    $products = $productsQuery->get();

    // Determine which category should be active in sidebar
    if ($search && !$selectedKategori) {
        // If search returned products, get the first product's category
        $activeCategory = $products->first()->category->id ?? null;
    } else {
        $activeCategory = $selectedKategori ?? null;
    }

    // Prepare message for view
    if ($search && $products->isEmpty()) {
        $message = "Produk atau kategori '<b>{$search}</b>' tidak ditemukan.";
    } elseif ($search) {
        $message = "Menemukan <b>{$products->count()}</b> produk untuk '<b>{$search}</b>'";
    } else {
        $message = null;
    }

    return view('user.shop', compact(
        'products',
        'categories',
        'message',
        'activeCategory', // for Blade highlighting
        'search',
        'selectedKategori'
    ));
}


    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('user.details', compact('product'));
    }
}
