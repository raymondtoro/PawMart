<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'keranjang';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'produk_id',
        'quantity',
        'price'
    ];

    /**
     * The user who owns this cart item
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * The product associated with this cart item
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'produk_id', 'id');
    }
}
