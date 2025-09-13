<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    // Show rating submission page (optional)
    public function index()
    {
        $products = Product::all(); // List products for dropdown
        return view('user.rating', compact('products'));
    }

    // Store rating from any source
    public function store(Request $request)
    {
        $data = $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'bintang'   => 'required|integer|min:1|max:5',
            'ulasan'    => 'nullable|string|max:1000',
        ]);

        Rating::create([
            'user_id'   => auth()->id(),
            'produk_id' => $data['produk_id'],
            'bintang'   => $data['bintang'],
            'ulasan'    => $data['ulasan'] ?? null,
        ]);

        return redirect()->route('about')
        ->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
