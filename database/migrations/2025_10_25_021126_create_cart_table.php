<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->id(); // kolom id (primary key)
            $table->unsignedBigInteger('user_id'); // relasi ke tabel users
            $table->unsignedBigInteger('id_produk'); // relasi ke tabel produk
            $table->integer('qty')->default(1); // jumlah produk
            $table->timestamps(); // created_at dan updated_at

            // Foreign key (jika relasi sudah ada)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
