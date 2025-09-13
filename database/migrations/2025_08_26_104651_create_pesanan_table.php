<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();

            // Relasi ke user
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Info pesanan
            $table->dateTime('tanggal_pesanan');
            $table->decimal('total_pesanan', 15, 2);

            // Status pesanan pakai ENUM
            $table->enum('status_pesanan', ['pending', 'diproses', 'dikirim', 'selesai', 'batal'])->default('pending');

            // Alamat pengiriman
            $table->string('alamat_pengiriman', 255);

            // Catatan opsional
            $table->string('catatan', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
