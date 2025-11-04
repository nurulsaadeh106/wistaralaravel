@include('admin.header')

<link rel="stylesheet" href="{{ asset('css/admin/produk.css') }}">

<style>
    /* 🌸 Background Fullscreen */
    body {
        font-family: 'Poppins', sans-serif;
        color: #0b1841;
        margin: 0;
        min-height: 100vh;
        background: url("{{ asset('img/background1.svg') }}") no-repeat center center fixed;
        background-size: cover;
        background-color: #ffffff;
    }
</style>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2>Kelola Produk</h2>
        <a href="{{ route('admin.produk.create') }}" class="btn btn-dark shadow-sm">
            + Tambah Produk
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    {{-- 🔍 Form Pencarian --}}
    <form method="GET" action="{{ route('admin.produk.index') }}" class="row g-3 mb-4">
        <div class="col-md-8">
            <input type="text" name="cari" value="{{ request('cari') }}" class="form-control shadow-sm"
                placeholder="🔍 Cari nama produk...">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-dark w-50 shadow-sm">Terapkan</button>
            <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary w-50 shadow-sm">Reset</a>
        </div>
    </form>

    <div class="table-container">
        <table class="table align-middle mb-0">
            <thead class="table-header">
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Marketplace</th>
                    <th>Status</th> <!-- ✅ Kolom status -->
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produk as $p)
                    <tr class="produk-row">
                        <td class="produk-cell text-center">{{ $loop->iteration }}</td>

                        @php
                            $fileName = basename($p->gambar);
                            $gambarPath = public_path('uploads/produk/' . $fileName);
                            $gambarUrl =
                                file_exists($gambarPath) && $fileName
                                    ? asset('uploads/produk/' . $fileName)
                                    : asset('img/no-image.jpg');
                        @endphp

                        <td class="produk-cell text-center">
                            @if ($p->gambar)
                                <img src="{{ $gambarUrl }}" alt="{{ $p->nama_produk }}" class="produk-img">
                            @else
                                <span class="text-muted fst-italic">Tidak ada</span>
                            @endif
                        </td>

                        <td class="produk-cell">
                            <div class="produk-name">{{ $p->nama_produk }}</div>
                            <div class="produk-desc">{{ Str::limit($p->deskripsi, 70) }}</div>
                        </td>

                        <td class="produk-cell text-center">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                        <td class="produk-cell text-center">{{ $p->kategori->nama_kategori ?? '-' }}</td>

                        <td class="produk-cell text-center">
                            @if ($p->link_shopee)
                                <a href="{{ $p->link_shopee }}" target="_blank" class="btn-shop me-1">Shopee</a>
                            @endif
                            @if ($p->link_tiktok)
                                <a href="{{ $p->link_tiktok }}" target="_blank" class="btn-tiktok">TikTok</a>
                            @endif
                            @if (!$p->link_shopee && !$p->link_tiktok)
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <!-- ✅ Status tampil di sini -->
                        <td class="produk-cell text-center">
                            @if ($p->status == 'nonaktif')
                                <span class="badge bg-secondary px-3 py-2">Nonaktif</span>
                            @else
                                <span class="badge bg-success px-3 py-2">Aktif</span>
                            @endif
                        </td>

                        <td class="produk-cell text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.produk.edit', $p->id_produk) }}" class="btn-action btn-edit"
                                    title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                @if ($p->status != 'nonaktif')
                                    <form action="{{ route('admin.produk.nonaktif', $p->id_produk) }}" method="POST"
                                        onsubmit="return confirm('Nonaktifkan produk ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-action btn-arsip" title="Nonaktifkan">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.produk.aktifkan', $p->id_produk) }}" method="POST"
                                        onsubmit="return confirm('Aktifkan kembali produk ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-action btn-restore" title="Aktifkan">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.produk.delete', $p->id_produk) }}" method="POST"
                                    onsubmit="return confirm('Hapus produk ini?')" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada produk yang tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($produk, 'links'))
        <div class="mt-4 d-flex justify-content-center">
            {{ $produk->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@include('admin.footer')
