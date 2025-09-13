<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rating;
use App\Models\Product;
use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // List all products
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.products.products', compact('products'));
    }

    // Show create form
    public function create()
    {
        $categories = Category::all();
        $promotions = Promotion::all();
        return view('admin.products.create', compact('categories', 'promotions'));
    }

    // Store new product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk'      => 'required|string|max:255',
            'harga_produk'     => 'required|numeric',
            'deskripsi_produk' => 'nullable|string',
            'stok_produk'      => 'required|integer|min:0',
            'kategori_id'      => 'required|exists:kategori,id',
            'promosi_id'       => 'nullable|exists:promosi,id',
            'gambar_produk.*'  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Upload images
        $images = [];
        if ($request->hasFile('gambar_produk')) {
            foreach ($request->file('gambar_produk') as $file) {
                $images[] = $file->store('produk_images', 'public');
            }
        }
        $validated['gambar_produk'] = $images;

        Product::create($validated);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil ditambahkan.');
    }

    // Show edit form
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $promotions = Promotion::all();

        return view('admin.products.edit', compact('product', 'categories', 'promotions'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_produk'      => 'required|string|max:255',
            'harga_produk'     => 'required|numeric',
            'deskripsi_produk' => 'required|string',
            'stok_produk'      => 'required|integer|min:0',
            'kategori_id'      => 'required|exists:kategori,id',
            'promosi_id'       => 'nullable|exists:promosi,id',
            'gambar_produk.*'  => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $product = Product::findOrFail($id);

        // Merge existing images with new uploads
        $images = $product->gambar_produk ?? [];
        if ($request->hasFile('gambar_produk')) {
            foreach ($request->file('gambar_produk') as $file) {
                $images[] = $file->store('produk_images', 'public');
            }
        }

        $product->update(array_merge($validated, [
            'gambar_produk' => $images,
        ]));

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil diperbarui!');
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete images
        if (!empty($product->gambar_produk) && is_array($product->gambar_produk)) {
            foreach ($product->gambar_produk as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil dihapus.');
    }

    // Show product details
    public function show($id)
{
    // Load product with category and ratings (including user)
    $product = Product::with(['category', 'ratings.user'])->findOrFail($id);

    $reviews = $product->ratings; // Use the relationship
    $relatedProducts = Product::where('kategori_id', $product->kategori_id)
                              ->where('id', '!=', $id)
                              ->take(6)
                              ->get();

    $averageRating = $reviews->count() ? $reviews->avg('bintang') : 0;
    $totalReviews = $reviews->count();

    return view('user.details', compact(
        'product',
        'reviews',
        'relatedProducts',
        'averageRating',
        'totalReviews'
    ));
}


    // Store user review
    public function storeReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produk,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|max:1000',
        ]);

        Rating::create([
            'user_id'   => auth()->id(),
            'produk_id' => $request->product_id,
            'bintang'   => $request->rating,
            'ulasan'    => $request->comment,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim!');
    }
}
