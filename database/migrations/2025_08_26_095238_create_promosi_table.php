<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('promosi', function (Blueprint $table) {
            $table->id();
            $table->string('judul_promosi');          // Required
            $table->text('deskripsi_promosi')->nullable(); // Optional
            $table->decimal('diskon', 5, 2)->nullable();   // Optional, can be empty
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('promosi');
    }
};
