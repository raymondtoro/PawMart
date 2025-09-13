<?php

namespace App\Http\Controllers\User;

use App\Models\Rating;
use App\Models\Product;
use App\Models\Promotion;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Show homepage (dashboard/shop).
     */
    public function index()
    {
        // Get weekend deals promotions (latest 5)
        $promotions = Promotion::latest()->take(5)->get();

        // Get products with their ratings and promotion
        $products = Product::with(['ratings', 'promotion', 'category'])
            ->get()
            ->map(function($product) {
                // Compute average rating (bintang)
                $product->avg_rating = $product->ratings->count() 
                    ? round($product->ratings->avg('bintang'), 1)
                    : 0;

                // Determine discounted price if promotion exists
                if ($product->promotion) {
                    $product->discounted_price = $product->harga_produk * (1 - $product->promotion->diskon / 100);
                } else {
                    $product->discounted_price = $product->harga_produk;
                }

                return $product;
            })
            ->sortByDesc('avg_rating') // sort by highest rating
            ->take(6); // limit to top 6

        // Get testimonials from Rating (ulasan)
        $testimonials = Rating::with(['user', 'product'])
                            ->whereNotNull('ulasan')
                            ->latest()
                            ->take(4)
                            ->get();

        return view('user.dashboard', compact('promotions', 'products', 'testimonials'));
    }

    /**
     * Show product details.
     */
    public function productDetails($id)
    {
        $product = Product::with(['ratings.user', 'promotion', 'category'])->findOrFail($id);

        // Compute average rating
        $product->avg_rating = $product->ratings->count() 
            ? round($product->ratings->avg('bintang'), 1) 
            : 0;

        // Compute discounted price
        if ($product->promotion) {
            $product->discounted_price = $product->harga_produk * (1 - $product->promotion->diskon / 100);
        } else {
            $product->discounted_price = $product->harga_produk;
        }

        return view('user.details', compact('product'));
    }

    public function about()
{
    // Get the latest 3 ratings for display
    $ratings = Rating::with(['user', 'product'])
                     ->latest()
                     ->take(3)
                     ->get();

    // Get total count to check if navigation is needed
    $totalRatings = Rating::count();

    return view('user.about', compact('ratings', 'totalRatings'));
}

}
