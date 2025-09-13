<?php

namespace App\Models;

use App\Models\PesananProduk;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'harga_produk',
        'deskripsi_produk',
        'stok_produk',
        'kategori_id',
        'promosi_id',
        'gambar_produk',
    ];

    // Cast JSON to array automatically
    protected $casts = [
        'gambar_produk' => 'array',
    ];

    /** 
     * Relasi ke kategori 
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    /** 
     * Relasi ke promosi 
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promosi_id');
    }

    /**
     * Relasi ke rating (ulasan).
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'produk_id');
    }

    /**
     * Relasi ke detail pesanan (pesanan_produk).
     * Bisa dipakai untuk hitung bestseller.
     */
    public function orderItems()
    {
        return $this->hasMany(PesananProduk::class, 'produk_id');
    }

    
}
