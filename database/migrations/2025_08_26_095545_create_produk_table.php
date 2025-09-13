<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();

            // Store multiple images as JSON
            $table->json('gambar_produk')->nullable();

            $table->string('nama_produk');
            $table->decimal('harga_produk', 15, 2);
            $table->text('deskripsi_produk')->nullable();
            $table->unsignedInteger('stok_produk');

            // Relationships
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->foreignId('promosi_id')->nullable()->constrained('promosi')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
