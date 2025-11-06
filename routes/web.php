<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Admin;
use App\Notifications\OrderStatusNotification;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('produk')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('katalog')
                ->with('error', 'Keranjang belanja Anda kosong.');
        }

        $total = $cartItems->sum(function($item) {
            return $item->qty * $item->produk->harga;
        });

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'tipe_order' => 'required|in:ambil,kirim',
            'metode_pembayaran' => 'required|in:bank_transfer,qris,cod',
            'nama' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required_if:tipe_order,kirim|string|nullable',
            'catatan' => 'nullable|string',
            'tanggal_ambil' => 'required|date|after:yesterday',
        ]);

        if ($validated['tipe_order'] === 'kirim') {
            return back()->with('error', 'Fitur pengiriman sedang dalam pengembangan 🚚✨');
        }

        try {
            DB::beginTransaction();

            // Ambil data keranjang
            $cartItems = Cart::where('user_id', Auth::id())
                ->with('produk')
                ->get();

            if ($cartItems->isEmpty()) {
                DB::rollback();
                return redirect()->route('katalog')
                    ->with('error', 'Keranjang belanja Anda kosong.');
            }

            // Hitung total
            $total = $cartItems->sum(function($item) {
                return $item->qty * $item->produk->harga;
            });

            // Buat order baru
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'tipe_order' => $validated['tipe_order'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'nama' => $validated['nama'],
                'telepon' => $validated['telepon'],
                'alamat' => $validated['alamat'],
                'catatan' => $validated['catatan'],
                'tanggal_ambil' => $validated['tanggal_ambil'],
            ]);

            // Pindahkan item dari cart ke order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'produk_id' => $item->produk_id,
                    'qty' => $item->qty,
                    'harga' => $item->produk->harga,
                    'subtotal' => $item->qty * $item->produk->harga
                ]);
            }

            // Kosongkan cart
            Cart::where('user_id', Auth::id())->delete();

            // Kirim notifikasi ke admin
            $admin = Admin::first();
            if ($admin) {
                $messageText = "Ada pesanan baru yang perlu diproses!";
                $admin->notify(new OrderStatusNotification($order, $messageText));
            }

            DB::commit();

            // Redirect sesuai metode pembayaran
            switch ($validated['metode_pembayaran']) {
                case 'bank_transfer':
                    return redirect()->route('checkout.bank-transfer', $order->id)
                        ->with('success', 'Pesanan berhasil dibuat!');
                case 'qris':
                    return redirect()->route('checkout.qris', $order->id)
                        ->with('success', 'Pesanan berhasil dibuat!');
                default:
                    return redirect()->route('user.orders')
                        ->with('success', 'Pesanan berhasil dibuat!');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
