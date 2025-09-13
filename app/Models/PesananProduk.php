<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class PesananProduk extends Model
{
    protected $table = 'pesanan_produk';

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'jumlah',
        'harga_saat_beli',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'pesanan_id', 'id');
    }

    public function produk()
{
    return $this->belongsTo(Product::class, 'produk_id');
}

// Alias to keep old code working
public function product()
{
    return $this->produk();
}

}
