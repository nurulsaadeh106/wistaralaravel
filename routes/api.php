<?php

use Illuminate\Support\Facades\Route;
use App\Models\Produk;
use App\Models\Berita;

// ✅ Endpoint untuk semua produk aktif
Route::get('/produk', function () {
    return response()->json(
        Produk::select('id','nama_produk','harga','stok')
              ->where('status', 'aktif')
              ->orderBy('created_at', 'desc')
              ->take(5) // tampilkan 5 produk saja
              ->get()
    );
});

// ✅ Endpoint Berita untuk Chatbot
Route::get('/berita', function () {
    return response()->json(
        \App\Models\Berita::select('id', 'judul', 'slug', 'tanggal')
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get()
    );
});

// 🔹 Login endpoint
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.'],
        ]);
    }

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});

// 🔹 Route API yang butuh autentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('reviews', ReviewController::class);
});

