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
        if ($id_produk) {
            // Jika beli langsung
            $produk = Produk::findOrFail($id_produk);
            $cartItems = collect([
                (object)[
                    'id_produk' => $produk->id_produk,
                    'qty' => 1,
                    'produk' => $produk
                ]
            ]);
        } else {
            // Jika checkout dari keranjang
            $cartItems = Cart::where('user_id', Auth::id())
                ->with('produk.kategori')
                ->get();
        }

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
            'tipe_order' => 'required|in:ambil,kirim',
            'alamat' => 'nullable|string',
            'tanggal_ambil' => 'required|date',
            'metode_pembayaran' => 'required|in:bank_transfer,qris,cod',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Ambil data cart user
            $cartItems = Cart::where('user_id', Auth::id())->with('produk')->get();

            // Jika tidak ada cart, bisa jadi checkout langsung
            if ($cartItems->isEmpty() && $request->has('produk_id')) {
                $produk = Produk::findOrFail($request->produk_id);
                $cartItems = collect([
                    (object)[
                        'id_produk' => $produk->id_produk,
                        'qty' => 1,
                        'produk' => $produk
                    ]
                ]);
            }

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Tidak ada item untuk diproses.');
            }

            // Hitung total
            $total = $cartItems->sum(fn($item) => $item->qty * $item->produk->harga);

            // Simpan order baru
            $order = Order::create([
                'user_id' => Auth::id(),
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'alamat' => $request->tipe_order === 'kirim' ? $request->alamat : '-',
                'catatan' => $request->catatan,
                'total' => $total,
                'status' => 'pending',
                'status_pembayaran' => 'belum_bayar',
                'tipe_order' => $request->tipe_order,
                'metode_pembayaran' => $request->metode_pembayaran,
                'tanggal_ambil' => $request->tanggal_ambil,
            ]);

            // Simpan item pesanan
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'id_produk' => $item->id_produk,
                    'qty' => $item->qty,
                    'harga' => $item->produk->harga,
                    'subtotal' => $item->qty * $item->produk->harga,
                ]);

                // Kurangi stok
                $item->produk->decrement('stok', $item->qty);
            }

            // Kosongkan keranjang
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            // Arahkan ke halaman pembayaran
            if ($order->metode_pembayaran === 'bank_transfer') {
                return redirect()->route('checkout.bank', $order->id)
                    ->with('success', 'Pesanan berhasil dibuat. Silakan transfer sesuai nominal.');
            } elseif ($order->metode_pembayaran === 'qris') {
                return redirect()->route('checkout.qris', $order->id)
                    ->with('success', 'Pesanan berhasil dibuat. Silakan scan QRIS untuk pembayaran.');
            }

            return redirect('/user/dashboard')->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}
