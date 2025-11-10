<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;

class CheckoutController extends Controller
{
    /**
     * 🧾 Menampilkan halaman checkout
     * Bisa dari keranjang atau langsung beli satu produk
     */
    public function index($id_produk = null)
    {
        // ✅ Jika ada id_produk → checkout langsung
        if ($id_produk) {
            $produk = Produk::findOrFail($id_produk);

            $cartItems = collect([
                (object)[
                    'id_produk' => $produk->id_produk,
                    'qty' => 1,
                    'produk' => $produk
                ]
            ]);
        } else {
            // ✅ Kalau tidak ada id_produk → ambil dari keranjang user
            $cartItems = Cart::where('user_id', Auth::id())
                ->with('produk.kategori')
                ->get();
        }

        // Jika tidak ada produk sama sekali
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        return view('checkout.index', compact('cartItems'));
    }

    /**
     * 💾 Proses checkout & simpan pesanan
     */
    public function process(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'tanggal_ambil' => 'required|date',
            'metode_pembayaran' => 'required|string',
        ]);

        $userId = Auth::id();

        // ⚡ Jika checkout langsung 1 produk
        if ($request->filled('id_produk')) {
            $produk = Produk::findOrFail($request->id_produk);

            $order = Order::create([
                'user_id' => $userId,
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'total' => $produk->harga,
                'status' => 'pending',
                'status_pembayaran' => 'belum_bayar',
                'metode_pembayaran' => $request->metode_pembayaran,
                'tanggal_ambil' => $request->tanggal_ambil,
                'tipe_order' => $request->tipe_order,
                'alamat' => $request->alamat,
                'catatan' => $request->catatan,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'id_produk' => $produk->id_produk,
                'qty' => 1,
                'harga' => $produk->harga,
                'subtotal' => $produk->harga,
            ]);

            $produk->decrement('stok', 1);
        } 
        else {
            // 🛒 Checkout dari keranjang
            $cartItems = Cart::where('user_id', $userId)->with('produk')->get();

            if ($cartItems->isEmpty()) {
                return back()->with('error', 'Tidak ada item untuk diproses.');
            }

            $total = $cartItems->sum(fn($item) => $item->qty * $item->produk->harga);

            $order = Order::create([
                'user_id' => $userId,
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'total' => $total,
                'status' => 'pending',
                'status_pembayaran' => 'belum_bayar',
                'metode_pembayaran' => $request->metode_pembayaran,
                'tanggal_ambil' => $request->tanggal_ambil,
                'tipe_order' => $request->tipe_order,
                'alamat' => $request->alamat,
                'catatan' => $request->catatan,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'id_produk' => $item->id_produk,
                    'qty' => $item->qty,
                    'harga' => $item->produk->harga,
                    'subtotal' => $item->qty * $item->produk->harga,
                ]);

                $item->produk->decrement('stok', $item->qty);
            }

            Cart::where('user_id', $userId)->delete();
        }

        // 🔁 Redirect sesuai metode pembayaran
        if ($request->metode_pembayaran === 'bank_transfer') {
            return redirect()->route('checkout.bank', $order->id);
        } elseif ($request->metode_pembayaran === 'qris') {
            return redirect()->route('checkout.qris', $order->id);
        } else {
            return redirect('/user/dashboard')->with('success', 'Pesanan berhasil dibuat!');
        }
    }
}
