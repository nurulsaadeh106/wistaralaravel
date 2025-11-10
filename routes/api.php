<?php

use Illuminate\Support\Facades\Route;
use App\Models\Produk;
use App\Models\Berita;

Route::get('/produk', function() {
    return response()->json(Produk::select('nama_produk','harga','stok')->get());
});

Route::get('/berita', function() {
    return response()->json(Berita::select('judul','tanggal','slug')->orderBy('tanggal','desc')->take(3)->get());
});


