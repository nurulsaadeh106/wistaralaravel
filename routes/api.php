<?php

use Illuminate\Support\Facades\Route;
use App\Models\Produk;
use App\Models\Berita;

/*
|--------------------------------------------------------------------------
| API Routes for Chatbot Wistara
|--------------------------------------------------------------------------
| Menyediakan data produk & berita dalam format JSON
| agar chatbot Node.js dapat mengaksesnya secara real-time.
*/

// ✅ Produk
Route::get('/produk', function () {
    return response()->json(
        Produk::select('id_produk', 'nama_produk', 'slug', 'harga', 'stok', 'gambar')
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
    );
});

// ✅ Berita
Route::get('/berita', function () {
    return response()->json(
        Berita::select('id', 'judul', 'slug', 'tanggal')
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get()
    );
});

// ✅ savechat
Route::post('/save-chat', function (Request $request) {
    DB::table('chat_sessions')->insert([
        'session_id'   => $request->input('session_id'),
        'user_message' => $request->input('user_message'),
        'bot_reply'    => $request->input('bot_reply'),
        'created_at'   => now(),
    ]);

    return response()->json(['success' => true]);
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

