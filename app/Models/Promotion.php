<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'promosi';

    // Mass assignable attributes
    protected $fillable = [
        'judul_promosi',
        'deskripsi_promosi',
        'diskon',
    ];

    /**
     * Get the products associated with this promotion.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'promosi_id', 'id');
    }
}
