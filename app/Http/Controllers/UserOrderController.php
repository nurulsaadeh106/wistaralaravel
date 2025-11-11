<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserOrderController extends Controller
{
    /**
     * 📄 Detail pesanan user
     */
    public function show($id)
    {
        // ID sekarang adalah string (contoh: WST-20251110-AX3P)
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['items.produk'])
            ->firstOrFail();

        return view('user.pesanan.show', compact('order'));
    }

    /**
     * 📤 Upload bukti pembayaran (Bank Transfer)
     */
    public function uploadBukti(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('metode_pembayaran', 'bank_transfer')
            ->where('status', 'pending')
            ->where('status_pembayaran', 'belum_bayar')
            ->firstOrFail();

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // 🗂️ Pastikan folder upload tersedia
        $uploadPath = public_path('uploads/bukti');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        // 📸 Upload file
        $file = $request->file('bukti_pembayaran');
        $filename = 'bukti_' . str_replace(['WST-', '-', ' '], '_', $order->id) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        // 📝 Update order
        $order->update([
            'bukti_pembayaran' => $filename,
            'status_pembayaran' => 'menunggu_verifikasi'
        ]);

        return back()->with('success', '✅ Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    /**
     * ❌ Batalkan pesanan dan kembalikan stok
     */
    public function cancel($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with('items.produk')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // 🛍️ Kembalikan stok produk
            foreach ($order->items as $item) {
                $item->produk->increment('stok', $item->qty);
            }

            // ❌ Update status pesanan
            $order->update([
                'status' => 'batal',
                'status_pembayaran' => 'gagal'
            ]);

            DB::commit();
            return redirect()->route('user.order.show', $id)
                ->with('success', '❌ Pesanan berhasil dibatalkan dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan: '.$e->getMessage());
        }
    }
}
