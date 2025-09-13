<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            // Relasi ke pesanan
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();

            // Info transaksi
            $table->dateTime('tanggal_transaksi');
            $table->decimal('total_transaksi', 15, 2);

            // Status transaksi pakai ENUM
            $table->enum('status_transaksi', ['pending', 'berhasil', 'gagal'])->default('pending');

            // Metode pembayaran
            $table->enum('metode_transaksi', ['cash', 'transfer', 'e-wallet']);

            // Biaya pengiriman
            $table->decimal('ongkir', 15, 2)->nullable();

            // Catatan tambahan
            $table->string('catatan', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
