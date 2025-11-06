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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // primary key
            $table->unsignedBigInteger('order_id'); // relasi ke tabel orders
            $table->unsignedBigInteger('id_produk'); // relasi ke tabel produk
            $table->integer('qty'); // jumlah produk
            $table->decimal('harga', 15, 2); // harga satuan
            $table->decimal('subtotal', 15, 2); // total harga qty * harga
            $table->timestamps(); // created_at & updated_at

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
