<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Produk;

class CartController extends Controller
{
    /**
     * 🧺 Menampilkan daftar keranjang user
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('produk')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->qty * $item->produk->harga;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * ➕ Tambahkan produk ke keranjang
     */
    public function add(Request $request, $produkId)
    {
        // Pastikan produk valid
        $produk = Produk::findOrFail($produkId);

        // Cek apakah produk sudah ada di keranjang user
        $cart = Cart::where('user_id', Auth::id())
            ->where('id_produk', $produkId) // pakai id_produk sesuai DB kamu
            ->first();

        if ($cart) {
            // Jika sudah ada, tambahkan qty
            $cart->increment('qty');
        } else {
            // Jika belum ada, buat baru
            Cart::create([
                'user_id' => Auth::id(),
                'id_produk' => $produkId,
                'qty' => 1,
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang 🛒');
    }

    /**
     * 🔄 Update jumlah produk di keranjang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cart->update(['qty' => $request->qty]);

        return back()->with('success', 'Jumlah produk berhasil diperbarui ✅');
    }

    /**
     * ❌ Hapus item dari keranjang
     */
    public function remove($id)
    {
        $cart = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cart->delete();

        return back()->with('success', 'Produk berhasil dihapus dari keranjang ❎');
    }
}
