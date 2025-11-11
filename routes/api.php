<?php

use Illuminate\Support\Facades\Route;
use App\Models\Produk;
use App\Models\Berita;
use App\Models\Order;
/*
|--------------------------------------------------------------------------
| API Routes for Chatbot Wistara
|--------------------------------------------------------------------------
| Menyediakan data produk pesanan berita dalam format JSON
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

// ✅ pesanan
Route::get('/order/{id}', function ($id) {
    $order = Order::where('id', $id)->first();

    if (!$order) {
        return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
    }

    return response()->json([
        'id' => $order->id,
        'nama' => $order->nama,
        'total' => $order->total,
        'status' => $order->status,
        'status_pembayaran' => $order->status_pembayaran,
        'tanggal' => $order->created_at->format('d/m/Y H:i')
    ]);
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

