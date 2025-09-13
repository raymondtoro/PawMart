<?php

namespace App\Http\Controllers\Admin;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PromotionController extends Controller
{
    /**
     * Display all promotions.
     */
    public function index()
    {
        $promotions = Promotion::all();
        return view('admin.promotion', compact('promotions')); 
        // view file: resources/views/admin/promotion.blade.php
    }

    /**
     * Store a newly created promotion (via popup form).
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_promosi' => 'required|string|max:255',
            'deskripsi_promosi' => 'nullable|string',
            'diskon' => 'nullable|numeric|min:0|max:100',
        ]);

        Promotion::create($request->only('judul_promosi', 'deskripsi_promosi', 'diskon'));

        return redirect()
            ->route('admin.promotion')
            ->with('success', 'Promosi berhasil ditambahkan.');
    }

    /**
     * Update an existing promotion (via popup form).
     */
    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'judul_promosi' => 'required|string|max:255',
            'deskripsi_promosi' => 'nullable|string',
            'diskon' => 'nullable|numeric|min:0|max:100',
        ]);

        $promotion->update($request->only('judul_promosi', 'deskripsi_promosi', 'diskon'));

        return redirect()
            ->route('admin.promotion')
            ->with('success', 'Promosi berhasil diperbarui.');
    }

    /**
     * Remove the specified promotion.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()
            ->route('admin.promotion')
            ->with('success', 'Promosi berhasil dihapus.');
    }
}
