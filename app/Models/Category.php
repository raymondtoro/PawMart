<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'kategori';
    protected $fillable = ['nama_kategori'];

    // Relationship to products
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'kategori_id');
    }
}
