@include('admin.header')

<style>
/* 🌸 Background Fullscreen */
body {
  font-family: 'Poppins', sans-serif;
  color: #0b1841;
  margin: 0;
  min-height: 100vh;
  background: url("{{ asset('img/background1.svg') }}") no-repeat center center fixed;
  background-size: cover; /* Fullscreen */
  background-color: #ffffff; /* fallback kalau SVG gagal load */
}

/* 🧭 Judul halaman */
h2 {
  font-weight: 700;
  color: #0b1841;
}

/* 🌈 Tabel produk */
.table-container {
  background: #ffffff;
  border-radius: 18px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  margin-top: 25px;
}

.table thead.table-header th {
  background-color: #081738;
  color: #ffffff;
  font-weight: 600;
  font-size: 15px;
  border: none;
  padding: 16px;
  text-align: center;
}

.produk-row {
  border-bottom: 1px solid #e5e7eb;
  transition: 0.25s ease;
}
.produk-row:hover {
  background-color: #f9fafb;
  transform: scale(1.001);
}

.produk-cell {
  padding: 20px 16px;
  vertical-align: middle;
}

.produk-img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 16px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.1);
}

.produk-name {
  font-weight: 600;
  font-size: 16px;
  color: #0b1841;
}
.produk-desc {
  font-size: 13px;
  color: #6b7280;
  margin-top: 3px;
}

/* 🎨 Tombol Aksi */
.btn-action {
  border: none;
  padding: 10px 12px;
  border-radius: 8px;
  color: white;
  transition: 0.3s ease;
}
.btn-edit { background-color: #fbbf24; }
.btn-edit:hover { background-color: #d1a106; }
.btn-delete { background-color: #dc2626; }
.btn-delete:hover { background-color: #b91c1c; }
.btn-arsip { background-color: #4b5563; }
.btn-arsip:hover { background-color: #374151; }
.btn-restore { background-color: #2563eb; }
.btn-restore:hover { background-color: #1e40af; }

.btn-shop, .btn-tiktok {
  border-radius: 6px;
  padding: 6px 12px;
  font-size: 13px;
  text-decoration: none;
  font-weight: 500;
  display: inline-block;
  box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.btn-shop { background-color: #f59e0b; color: #fff; }
.btn-shop:hover { background-color: #d97706; }
.btn-tiktok { background-color: #000; color: #fff; }
.btn-tiktok:hover { background-color: #1c1c1c; }

.pagination { justify-content: center; }
.pagination .page-link { color: #0b1841; border-radius: 6px; }
.pagination .page-item.active .page-link {
  background-color: #0b1841;
  border-color: #0b1841;
}

.form-control {
  border-radius: 10px;
  border: 1px solid #cbd5e1;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #081738;
  box-shadow: 0 0 0 0.15rem rgba(8,23,56,0.25);
}

.btn-dark {
  background-color: #081738;
  border: none;
  font-weight: 500;
  border-radius: 12px;
  padding: 10px 18px;
}
.btn-dark:hover {
  background-color: #001b66;
}
</style>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h2>Kelola Produk</h2>
    <a href="{{ route('admin.produk.create') }}" class="btn btn-dark shadow-sm">
      + Tambah Produk
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
  @endif

  {{-- 🔍 Form Pencarian --}}
  <form method="GET" action="{{ route('admin.produk.index') }}" class="row g-3 mb-4">
    <div class="col-md-8">
      <input type="text" name="cari" value="{{ request('cari') }}" class="form-control shadow-sm" placeholder="🔍 Cari nama produk...">
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
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($produk as $p)
        <tr class="produk-row">
          <td class="produk-cell text-center">{{ $loop->iteration }}</td>

          @php
            $fileName = basename($p->gambar);
            $gambarPath = public_path('uploads/produk/'.$fileName);
            $gambarUrl = (file_exists($gambarPath) && $fileName)
                ? asset('uploads/produk/'.$fileName)
                : asset('img/no-image.jpg');
          @endphp

          <td class="produk-cell text-center">
            @if($p->gambar)
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
            @if($p->link_shopee)
              <a href="{{ $p->link_shopee }}" target="_blank" class="btn-shop me-1">Shopee</a>
            @endif
            @if($p->link_tiktok)
              <a href="{{ $p->link_tiktok }}" target="_blank" class="btn-tiktok">TikTok</a>
            @endif
            @if(!$p->link_shopee && !$p->link_tiktok)
              <span class="text-muted">-</span>
            @endif
          </td>

          <td class="produk-cell text-center">
            @if($p->status == 'nonaktif')
              <span class="badge bg-secondary">Nonaktif</span>
            @else
              <span class="badge bg-success">Aktif</span>
            @endif
          </td>

          <td class="produk-cell text-center">
            <div class="d-flex justify-content-center gap-2">
              <a href="{{ route('admin.produk.edit', $p->id_produk) }}" class="btn-action btn-edit" title="Edit">
                <i class="bi bi-pencil-fill"></i>
              </a>

              @if($p->status != 'nonaktif')
              <form action="{{ route('admin.produk.nonaktif', $p->id_produk) }}" method="POST" onsubmit="return confirm('Nonaktifkan produk ini?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn-action btn-arsip" title="Nonaktifkan">
                  <i class="bi bi-slash-circle"></i>
                </button>
              </form>
              @else
              <form action="{{ route('admin.produk.aktifkan', $p->id_produk) }}" method="POST" onsubmit="return confirm('Aktifkan kembali produk ini?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn-action btn-restore" title="Aktifkan">
                  <i class="bi bi-check-circle"></i>
                </button>
              </form>
              @endif

              <form action="{{ route('admin.produk.delete', $p->id_produk) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')" class="d-inline">
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

  @if(method_exists($produk, 'links'))
    <div class="mt-4 d-flex justify-content-center">
      {{ $produk->links('pagination::bootstrap-5') }}
    </div>
  @endif
</div>

@include('admin.footer')
