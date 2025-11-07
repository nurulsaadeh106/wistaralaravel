<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\Berita;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ChatbotApiController extends Controller
{
    /**
     * Endpoint utama chatbot (hybrid):
     * - Menu angka 1–4/0 (stateful ala WA)
     * - Chat bebas pintar (produk/kategori/berita)
     */
    public function reply(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'state'   => 'nullable|string|max:50', // 'menu' | 'waiting_product_name'
        ]);

        $raw   = trim($request->message);
        $msg   = Str::lower($raw);
        $state = $request->input('state', 'menu');

        // perintah universal untuk reset menu
        if (in_array($msg, ['menu','/menu','/start','help'])) {
            return $this->menuResponse();
        }

        // ======== MODE MENU (angka 1–4/0) ========
        if ($state === 'menu') {
            // jika user pilih angka valid → jalankan menu
            if (in_array($msg, ['1','2','3','4','0'])) {
                return $this->handleMenuSelection($msg);
            }

            // jika bukan angka → jalankan chat bebas pintar
            if ($smart = $this->handleFreeChatSmart($raw, $msg)) {
                return $smart;
            }

            // kalau tidak dikenali → tampilkan menu lagi
            return $this->menuResponse("Maaf, pilihannya 1–4 ya. Ini menunya 👇");
        }

        // ======== MODE MENUNGGU NAMA PRODUK (setelah pilih 2) ========
        if ($state === 'waiting_product_name') {
            return $this->handleProductLookup($raw);
        }

        // default jika state tidak dikenal
        return $this->menuResponse();
    }

    /* =========================================================================
       HANDLER MENU
       ========================================================================= */

private function handleMenuSelection(string $choice)
{
    switch ($choice) {
        // 🧵 Menu 1 — Katalog Produk
        case '1':
            $produkList = Produk::where('status', 'aktif')
                ->orderByDesc('tanggal_upload')
                ->limit(4)
                ->get();

            if ($produkList->isEmpty()) {
                return $this->okMenuOnly("😅 Belum ada produk yang ditambahkan. Silakan ketik <b>menu</b> untuk kembali.");
            }

            $produkText = "🧵 <b>Katalog Produk Terbaru</b><br><br>";
            foreach ($produkList as $p) {
                $harga = number_format($p->harga, 0, ',', '.');
                $stok = $p->stok > 0 ? "✅ Tersedia" : "❌ Habis";
                $produkText .= "<b>{$p->nama_produk}</b><br>"
                             . "💰 Rp {$harga} | {$stok}<br>"
                             . "<a href='" . url('checkout/' . $p->id_produk) . "' target='_blank'>🛒 Checkout</a><br><br>";
            }

            $produkText .= "Ketik <b>menu</b> untuk kembali ke menu utama.";
            return $this->okMenuOnly($produkText);

        // 🧾 Menu 2 — Cek Stok
        case '2':
            return $this->okMenuOnly("🧾 <b>Cek Stok Produk</b><br>Ketik nama produk yang ingin kamu cari.<br><br>Ketik <b>menu</b> untuk kembali ke menu utama.");

        // 📰 Menu 3 — Berita Terbaru
        case '3':
            $news = Berita::orderByDesc('tanggal')->limit(3)->get();
            if ($news->isEmpty()) {
                return $this->okMenuOnly("📭 Belum ada berita terbaru.<br><br>Ketik <b>menu</b> untuk kembali.");
            }

            $text = "📰 <b>Berita Terbaru Batik Wistara</b><br><br>";
            foreach ($news as $b) {
                $tgl = date('d M Y', strtotime($b->tanggal));
                $text .= "<b>{$b->judul}</b> — {$tgl}<br>";
                if ($b->tautan_sumber) {
                    $text .= "<a href='{$b->tautan_sumber}' target='_blank'>Baca Selengkapnya 🔗</a><br>";
                }
                $text .= "<br>";
            }

            $text .= "Ketik <b>menu</b> untuk kembali ke menu utama.";
            return $this->okMenuOnly($text);

        // 📍 Menu 4 — Alamat
        case '4':
            $alamat = "📍 <b>Alamat Toko:</b><br>Jl. Tambak Medokan Ayu VI C No.56B, Surabaya<br>"
                    . "<a href='https://maps.app.goo.gl/WqHPo5eNBDqHykhM8' target='_blank'>Lihat di Google Maps</a><br><br>"
                    . "🕓 Jam Operasional: 09.00 – 17.00 WIB.<br><br>"
                    . "Ketik <b>menu</b> untuk kembali ke menu utama.";
            return $this->okMenuOnly($alamat);

        // 👩‍💼 Menu 0 — Hubungi Admin
        case '0':
            $reply = "👩‍💼 <b>Hubungi Admin Batik Wistara</b><br>"
                   . "<a href='https://wa.me/62895381110035' target='_blank'>WhatsApp Admin</a><br><br>"
                   . "Ketik <b>menu</b> untuk kembali ke menu utama.";
            return $this->okMenuOnly($reply);
    }

    return $this->menuResponse("Maaf, pilihannya 1–4 ya. Ini menunya 👇");
}


    // ======== TAMBAHKAN helper ini ========
    private function okMenuOnly(string $replyHtml)
    {
        return response()->json([
            'reply'         => nl2br($replyHtml),
            'next_state'    => 'menu',       // tetap di state menu
            'quick_replies' => ['menu'],     // hanya tampilkan tombol "menu"
            'status'        => 'success',
        ]);
    }

    private function menuResponse(string $prefix = null)
    {
        $text = ($prefix ? $prefix . "\n\n" : "")
              . "Selamat datang di <b>WistaraBot</b> 👋\n"
              . "Silakan pilih angka:\n\n"
              . "1. Katalog Produk\n"
              . "2. Cek Stok Produk\n"
              . "3. Berita Terbaru\n"
              . "4. Alamat & Jam Buka\n"
              . "0. Hubungi Admin";

        return $this->ok(nl2br($text), 'menu');
    }

    /* =========================================================================
       CHAT BEBAS PINTAR (produk/kategori/berita/faq singkat)
       ========================================================================= */

    private function handleFreeChatSmart(string $raw, string $msg)
    {
        // intent cepat
        if (Str::contains($msg, ['berita','news','kabar'])) {
            return $this->ok($this->latestNews(), 'menu');
        }
        if (Str::contains($msg, ['alamat','lokasi','maps'])) {
            $reply = "📍 Kami di <b>Jl. Tambak Medokan Ayu VI C No.56B, Surabaya</b>.<br>"
                   . "<a href='https://maps.app.goo.gl/WqHPo5eNBDqHykhM8' target='_blank'>Buka Google Maps</a>";
            return $this->ok($reply, 'menu');
        }
        if (Str::contains($msg, ['jam','buka','operasional'])) {
            return $this->ok("🕒 Jam operasional: <b>09.00 – 17.00 WIB</b> setiap hari.", 'menu');
        }

        $tokens = $this->normalizeAndExpand($msg);

        // prioritas: produk → kategori → berita
        if ($produkReply = $this->searchProdukSmart($tokens, $msg)) {
            return $this->ok($produkReply, 'menu');
        }
        if ($kategoriReply = $this->searchKategoriSmart($tokens)) {
            return $this->ok($kategoriReply, 'menu');
        }

        // rekomendasi / fallback
        $suggest = $this->suggestProduk();
        if ($suggest) {
            return $this->ok("🙇‍♀️ Belum ketemu yang cocok. Mungkin kamu suka ini:<br><br>".$suggest, 'menu');
        }

        $fallback = "🤔 Saya belum menemukan info yang cocok.<br>"
                  . "Tanyakan langsung ke admin: "
                  . "<a href='https://wa.me/62895381110035?text=".urlencode($raw)."' target='_blank'>WhatsApp Batik Wistara</a>";
        return $this->ok($fallback, 'menu');
    }

    /* =========================================================================
       FITUR MENU #2: CEK STOK (menunggu nama produk)
       ========================================================================= */

    private function handleProductLookup(string $raw)
    {
        $msg = Str::lower($raw);
        $produk = Produk::where('status','aktif')
            ->where(function($q) use ($msg) {
                $q->whereRaw('LOWER(nama_produk) LIKE ?', ["%{$msg}%"])
                  ->orWhereRaw('LOWER(deskripsi) LIKE ?', ["%{$msg}%"]);
            })
            ->limit(4)->get();

        if ($produk->isEmpty()) {
            $suggest = $this->listLatestProducts(3);
            $text = "🙏 Maaf, produk <b>{$this->e($raw)}</b> belum ditemukan.\n"
                  . "Mungkin kamu tertarik ini:\n\n{$suggest}\n\nKetik <b>menu</b> untuk kembali.";
            return $this->ok(nl2br($text), 'menu');
        }

        $lines = $produk->map(fn($p) => $this->productLine($p))->implode("\n");
        $text  = "✅ Berikut hasil untuk: <b>".$this->e($raw)."</b>\n\n{$lines}\n\nKetik <b>menu</b> untuk kembali.";
        return $this->ok(nl2br($text), 'menu');
    }

    /* =========================================================================
       PENCARIAN PINTAR
       ========================================================================= */

    private function normalizeAndExpand(string $message): array
    {
        // bersihkan karakter non-alfanumerik
        $clean = preg_replace('/[^a-z0-9\s\-]/', ' ', $message);
        $clean = preg_replace('/\s+/', ' ', $clean);

        // stopwords ringan
        $stop = ['yang','dan','di','ke','dari','untuk','buat','minta','dong','kak','bang','mas','mbak',
                 'tolong','apa','ada','nya','itu','ini','saya','aku','mau','produk','kategori','batik',
                 'baju','kemeja','outer'];

        $baseTokens = collect(explode(' ', trim($clean)))
            ->filter(fn($t) => strlen($t) >= 2 && !in_array($t, $stop))
            ->values();

        // sinonim dasar
        $syn = [
            'wanita' => ['wanita','perempuan','cewek','ladies'],
            'pria'   => ['pria','laki','cowok','men'],
            'tulis'  => ['tulis','handmade','tulisan'],
            'cap'    => ['cap','printing','cetakan'],
            'parang' => ['parang','lereng','gagrak'],
            'mega'   => ['mega','mendung','mega-mendung','megamendung'],
            'merak'  => ['merak','peacock'],
            'modern' => ['modern','kontemporer','kekinian'],
            'klasik' => ['klasik','tradisional','classic'],
            'outer'  => ['outer','luaran','cardigan'],
        ];

        $expanded = collect();
        foreach ($baseTokens as $t) {
            $expanded->push($t);
            foreach ($syn as $key => $arr) {
                if (in_array($t, $arr, true) || $t === $key) {
                    $expanded = $expanded->merge($arr);
                }
            }
        }

        return $expanded->unique()->values()->all();
    }

    private function searchProdukSmart(array $tokens, string $originalMsg): ?string
    {
        if (empty($tokens)) return null;

        $q = Produk::with('kategori')
            ->where('status', 'aktif')
            ->where(function ($qq) use ($tokens) {
                foreach ($tokens as $t) {
                    $qq->orWhereRaw('LOWER(nama_produk) LIKE ?', ["%{$t}%"])
                       ->orWhereRaw('LOWER(deskripsi) LIKE ?', ["%{$t}%"]);
                }
            });

        $produk = $q->limit(4)->get();
        if ($produk->isEmpty()) return null;

        $lines = $produk->map(fn($p) => $this->productLine($p))->implode("\n");
        $text  = "🔎 Kamu mencari: <b>".$this->e($originalMsg)."</b>\n\n{$lines}\n\nKetik <b>menu</b> untuk kembali.";
        return nl2br($text);
    }

    private function searchKategoriSmart(array $tokens): ?string
    {
        if (empty($tokens)) return null;

        $kategori = KategoriProduk::where(function($q) use ($tokens) {
            foreach ($tokens as $t) {
                $q->orWhereRaw('LOWER(nama_kategori) LIKE ?', ["%{$t}%"]);
            }
        })->first();

        if (!$kategori) return null;

        $produk = Produk::where('status','aktif')
            ->where('id_kategori', $kategori->id_kategori)
            ->orderByDesc('tanggal_upload')
            ->limit(4)->get();

        if ($produk->isEmpty()) {
            return "📦 Belum ada produk aktif di kategori <b>".$this->e($kategori->nama_kategori)."</b>.";
        }

        $lines = $produk->map(fn($p) => $this->productLine($p))->implode("\n");
        $cta   = "<div style='margin-top:6px'><a href='".url('/katalog?kategori='.$kategori->id_kategori)."' target='_blank'>Lihat lebih banyak ↗️</a></div>";

        return "🧩 Kategori: <b>".$this->e($kategori->nama_kategori)."</b>\n\n".nl2br($lines)."<br>".$cta;
    }

    private function latestNews(): string
    {
        $news = Cache::remember('chatbot_news', 60, fn() =>
            Berita::orderBy('tanggal','desc')->limit(3)->get()
        );

        if ($news->isEmpty()) {
            return "📢 Saat ini belum ada berita terbaru.\n\nKetik <b>menu</b> untuk kembali.";
        }

        $lines = $news->map(function($b){
            $tgl = date('d M Y', strtotime($b->tanggal));
            $row = "• <b>".$this->e($b->judul)."</b> — {$tgl}";
            if ($b->tautan_sumber) $row .= "<br><a href='{$b->tautan_sumber}' target='_blank'>Baca selengkapnya 🔗</a>";
            return $row;
        })->implode("<br><br>");

        return "📰 <b>Berita Terbaru</b>\n\n{$lines}\n\nKetik <b>menu</b> untuk kembali.";
    }

    private function suggestProduk(): ?string
    {
        $produk = Produk::where('status','aktif')
            ->orderByDesc('tanggal_upload')
            ->limit(3)->get();

        if ($produk->isEmpty()) return null;

        return nl2br($produk->map(fn($p) => $this->productLine($p))->implode("\n"));
    }

    /* =========================================================================
       UTILITAS RENDER & RESPONSE
       ========================================================================= */

    private function productLine($p): string
    {
        $harga = number_format($p->harga, 0, ',', '.');
        $stok  = $p->stok > 0 ? 'Tersedia ✅' : 'Habis ❌';
        $links = [];

        if ($p->link_shopee) $links[] = "<a href='{$p->link_shopee}' target='_blank'>Shopee</a>";
        if ($p->link_tiktok) $links[] = "<a href='{$p->link_tiktok}' target='_blank'>TikTokShop</a>";
        $links[] = "<a href='".url('checkout/'.$p->id_produk)."' target='_blank'>Checkout</a>";

        $linksHtml = implode(' · ', $links);

        return "• <b>".$this->e($p->nama_produk)."</b>\n  Rp {$harga} • {$stok}\n  {$linksHtml}";
    }

    private function ok(string $reply, string $nextState)
    {
        return response()->json([
            'reply'         => $reply,          // HTML yang siap ditampilkan di bubble bot
            'next_state'    => $nextState,      // 'menu' | 'waiting_product_name'
            'quick_replies' => ['1','2','3','4','0','menu'],
            'status'        => 'success',
        ]);
    }

    private function e(?string $text): string
    {
        return e((string) $text);
    }
}