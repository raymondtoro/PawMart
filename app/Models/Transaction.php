<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'transaksi';

    // Mass assignable attributes
    protected $fillable = [
        'pesanan_id',
        'tanggal_transaksi',
        'total_transaksi',
        'status_transaksi',
        'metode_transaksi',
        'alamat_pengiriman',
        'ongkir',
        'catatan',
    ];

    /**
     * Get the order (pesanan) associated with this transaction.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'pesanan_id', 'id');
    }
}
