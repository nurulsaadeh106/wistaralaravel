<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Produk;

class CartController extends Controller
{
    /**
     * 🛒 Tampilkan halaman keranjang
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('produk')
            ->get();

        // Hitung total harga
        $total = $cartItems->sum(function ($item) {
            return $item->produk->harga * $item->qty;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * ➕ Tambah produk ke keranjang
     */
    public function add(Request $request, $produkId)
    {
        $produk = Produk::find($produkId);
        if (!$produk) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        $qty = max((int) $request->input('qty', 1), 1);

        // Cek stok
        if ($qty > $produk->stok) {
            return back()->with('error', 'Jumlah melebihi stok produk.');
        }

        // Cek apakah sudah ada produk ini di keranjang
        $existing = Cart::where('user_id', Auth::id())
            ->where('produk_id', $produkId)
            ->first();

        if ($existing) {
            // Update qty
            $newQty = $existing->qty + $qty;

            if ($newQty > $produk->stok) {
                return back()->with('error', 'Jumlah total melebihi stok.');
            }

            $existing->update([
                'qty' => $newQty,
                'updated_at' => now(),
            ]);
        } else {
            // Tambah item baru
            Cart::create([
                'user_id' => Auth::id(),
                'produk_id' => $produkId,
                'qty' => $qty,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * 🗑️ Hapus item dari keranjang
     */
    public function remove($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * 🔄 Update jumlah qty dari keranjang (opsional)
     */
    public function update(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $qty = max((int) $request->input('qty', 1), 1);
        $produk = Produk::find($cartItem->produk_id);

        if ($qty > $produk->stok) {
            return back()->with('error', 'Jumlah melebihi stok produk.');
        }

        $cartItem->update([
            'qty' => $qty,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }
}
