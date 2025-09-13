<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'pesanan';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'nama',                 // user name
        'tanggal_pesanan',      // order date
        'total_pesanan',          // total order price
        'status_pesanan',       // order status
        'alamat_pengiriman',    // shipping address
    ];

    /**
     * Get the user that owns this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the order items associated with this order.
     */
    public function orderItems()
    {
        return $this->hasMany(PesananProduk::class, 'pesanan_id', 'id');
    }
    /**
 * Get all products associated with this order through PesananProduk.
 */
public function produk()
{
    return $this->hasManyThrough(
        Product::class,    // Final model
        PesananProduk::class, // Intermediate model
        'pesanan_id',   // Foreign key on PesananProduk pointing to Order
        'id',           // Foreign key on Produk (usually 'id')
        'id',           // Local key on Order
        'produk_id'     // Foreign key on PesananProduk pointing to Produk
    );
}
// <-- Add this
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'pesanan_id', 'id');
    }

}
