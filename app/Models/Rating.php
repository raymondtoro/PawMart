<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'rating';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'produk_id',
        'bintang',
        'ulasan',
    ];

    // Cast bintang to integer for easier calculations
    protected $casts = [
        'bintang' => 'integer',
    ];

    /**
     * Get the product that this rating belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'produk_id', 'id');
    }

    /**
     * Get the user who made this rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
