<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('keranjang', function (Blueprint $table) {
        $table->id();

        // User who owns the cart
        $table->foreignId('user_id')
              ->constrained('users')
              ->onDelete('cascade');

        // Product added to cart
        $table->foreignId('produk_id')
              ->constrained('produk')
              ->onDelete('cascade');

        // Quantity of product
        $table->unsignedInteger('quantity')->default(1);

        // Price snapshot (in case product price changes later)
        $table->decimal('price', 15, 2);


        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
