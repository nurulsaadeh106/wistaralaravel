<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\Berita;

class ChatbotApiController extends Controller
{
    public function reply(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = strtolower(trim($request->message));

        // 1️⃣ Cek apakah user tanya produk
        if ($produkReply = $this->searchProduk($message)) {
            return response()->json([
                'reply' => $produkReply,
                'source' => 'produk_db',
                'status' => 'success'
            ]);
        }

        // 2️⃣ Cek apakah user tanya kategori
        if ($kategoriReply = $this->searchKategori($message)) {
            return response()->json([
                'reply' => $kategoriReply,
                'source' => 'kategori_db',
                'status' => 'success'
            ]);
        }

        // 3️⃣ Cek apakah user tanya berita
        if ($beritaReply = $this->searchBerita($message)) {
            return response()->json([
                'reply' => $beritaReply,
                'source' => 'berita_db',
                'status' => 'success'
            ]);
        }

        // 4️⃣ Fallback: FAQ umum
        $fallback = $this->generateReply($message);

        return response()->json([
            'reply' => $fallback,
            'source' => 'default',
            'status' => 'success'
        ]);
    }

    /**
     * 🔍 Cari produk berdasarkan kata kunci
     */
    private function searchProduk(string $message): ?string
    {
        $produkList = Produk::where('status', 'aktif')
            ->where(function ($q) use ($message) {
                $q->whereRaw('LOWER(nama_produk) LIKE ?', ["%{$message}%"])
                  ->orWhereRaw('LOWER(deskripsi) LIKE ?', ["%{$message}%"]);
            })
            ->limit(3)
            ->get();

        if ($produkList->isEmpty()) return null;

        $reply = "🧵 Berikut produk yang cocok dengan pencarian kamu:<br><br>";

        foreach ($produkList as $p) {
            $harga = number_format($p->harga, 0, ',', '.');
            $stok = $p->stok > 0 ? "<span style='color:green'>Tersedia ✅</span>" : "<span style='color:red'>Habis ❌</span>";

            $reply .= "<b>{$p->nama_produk}</b><br>
                       💰 Harga: Rp {$harga}<br>
                       📦 Stok: {$stok}<br>";

            if ($p->link_shopee || $p->link_tiktok) {
                $reply .= "🛍️ Beli di: ";
                if ($p->link_shopee) {
                    $reply .= "<a href='{$p->link_shopee}' target='_blank'>Shopee</a> ";
                }
                if ($p->link_tiktok) {
                    $reply .= "<a href='{$p->link_tiktok}' target='_blank'>TikTokShop</a>";
                }
                $reply .= "<br>";
            }

            $reply .= "ℹ️ {$p->deskripsi}<br><br>";
        }

        return $reply;
    }

    /**
     * 🧩 Cari kategori produk
     */
    private function searchKategori(string $message): ?string
    {
        $kategori = KategoriProduk::whereRaw('LOWER(nama_kategori) LIKE ?', ["%{$message}%"])->first();

        if (!$kategori) return null;

        $produkList = Produk::where('id_kategori', $kategori->id_kategori)
            ->where('status', 'aktif')
            ->limit(3)
            ->get();

        if ($produkList->isEmpty()) {
            return "📦 Saat ini belum ada produk aktif di kategori <b>{$kategori->nama_kategori}</b>.";
        }

        $reply = "🧩 Kategori: <b>{$kategori->nama_kategori}</b><br>
                  Berikut produk dalam kategori ini:<br><br>";

        foreach ($produkList as $p) {
            $reply .= "• {$p->nama_produk} — Rp " . number_format($p->harga, 0, ',', '.') . "<br>";
        }

        return $reply;
    }

    /**
     * 📰 Ambil berita terbaru
     */
    private function searchBerita(string $message): ?string
    {
        if (!str_contains($message, 'berita')) return null;

        $beritaList = Berita::orderBy('tanggal', 'desc')->limit(3)->get();

        if ($beritaList->isEmpty()) {
            return "📰 Saat ini belum ada berita terbaru dari Batik Wistara.";
        }

        $reply = "📰 Berikut berita terbaru Batik Wistara:<br><br>";

        foreach ($beritaList as $b) {
            $tanggal = date('d M Y', strtotime($b->tanggal));
            $reply .= "<b>{$b->judul}</b><br>
                       📅 {$tanggal}<br>";

            if ($b->tautan_sumber) {
                $reply .= "<a href='{$b->tautan_sumber}' target='_blank'>Baca selengkapnya 🔗</a><br>";
            }

            $reply .= "<br>";
        }

        return $reply;
    }

    /**
     * 💬 Balasan umum (default)
     */
    private function generateReply(string $message): string
    {
        if (str_contains($message, 'halo') || str_contains($message, 'hai')) {
            return "👋 Halo! Saya <b>WistaraBot</b> siap bantu kamu.<br>
                    Coba tanya tentang <i>produk</i>, <i>kategori</i>, atau <i>berita terbaru</i> 🧵";
        }

        if (str_contains($message, 'alamat') || str_contains($message, 'lokasi')) {
            return "📍 Kami berlokasi di <b>Jl. Tambak Medokan Ayu VI C No.56B, Surabaya</b>.<br>
                    Klik untuk arah: <a href='https://maps.app.goo.gl/WqHPo5eNBDqHykhM8' target='_blank'>Google Maps</a>";
        }

        if (str_contains($message, 'jam') || str_contains($message, 'buka')) {
            return "🕒 Kami buka setiap hari pukul <b>09.00 - 17.00 WIB</b>.";
        }

        if (str_contains($message, 'terima kasih')) {
            return "🙏 Sama-sama! Semoga harimu menyenangkan 💛";
        }

        return "🤔 Saya belum paham pertanyaan itu.<br>
                Coba tanya tentang <i>produk</i>, <i>kategori</i>, atau <i>berita terbaru</i>.";
    }
}
