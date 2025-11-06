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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // primary key
            $table->unsignedBigInteger('user_id'); // relasi ke tabel users
            $table->string('nama'); // nama pemesan
            $table->string('telepon'); // no telepon
            $table->text('alamat'); // alamat pengiriman
            $table->text('catatan')->nullable(); // catatan tambahan
            $table->decimal('total', 15, 2); // total harga (maksimal 999 triliun)
            $table->enum('status', ['pending', 'proses', 'selesai', 'batal'])->default('pending'); // status order
            $table->enum('tipe_order', ['ambil', 'kirim']); // tipe pengambilan
            $table->string('metode_pembayaran');
            $table->date('tanggal_ambil')->nullable();
            $table->datetime('ambil')->nullable(); // waktu ambil
            $table->datetime('kirim')->nullable(); // waktu kirim
            $table->timestamps(); // created_at & updated_at

            // Foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
