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

