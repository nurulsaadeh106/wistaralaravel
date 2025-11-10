@include('inc.header') 

<section class="cart-section position-relative" 
         style="background: url('{{ asset('img/bghero.svg') }}') center/cover no-repeat; min-height: 100vh; margin-top: -80px; padding-bottom: 60px;">

  <!-- Overlay -->
  <div class="position-absolute top-0 start-0 w-100 h-100" 
       style="background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(3px); z-index: 1;"></div>

  <div class="container position-relative" style="z-index: 2; padding-top: 140px;">

    <!-- Title -->
    <h2 class="fw-bold mb-4 text-center text-gold">
      Keranjang Belanja Anda
    </h2>

    {{-- Notifikasi --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3">
        <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3">
        <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if($cartItems->count() > 0)
      <!-- Tabel Produk -->
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead style="background-color: #1e1e1e; color: #f8f9fa;">
              <tr>
                <th scope="col" style="width: 40%">Produk</th>
                <th scope="col" class="text-center">Harga</th>
                <th scope="col" class="text-center">Jumlah</th>
                <th scope="col" class="text-center">Total</th>
                <th scope="col" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($cartItems as $item)
                @php
                  $gambarPath = public_path($item->produk->gambar ?? '');
                  $gambarUrl = ($item->produk && $item->produk->gambar && file_exists($gambarPath)) 
                                ? asset($item->produk->gambar)
                                : asset('img/no-image.jpg');
                @endphp

                <tr class="align-middle">
                  <!-- Produk -->
                  <td class="d-flex align-items-center gap-3 py-3">
                    <img src="{{ $gambarUrl }}"
                         alt="{{ $item->produk->nama_produk ?? 'Produk' }}"
                         class="rounded shadow-sm border"
                         style="width: 75px; height: 75px; object-fit: cover;">
                    <div>
                      <h6 class="fw-bold mb-1 text-dark">{{ $item->produk->nama_produk ?? 'Produk tidak tersedia' }}</h6>
                      <small class="text-muted">{{ $item->produk->nama_kategori ?? '-' }}</small>
                    </div>
                  </td>

                  <!-- Harga -->
                  <td class="text-center fw-semibold text-dark">
                    Rp {{ number_format(optional($item->produk)->harga ?? 0, 0, ',', '.') }}
                  </td>

                  <!-- Jumlah -->
                  <td class="text-center">
                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline-flex align-items-center justify-content-center">
                      @csrf
                      @method('PUT')
                      <div class="input-group input-group-sm" style="max-width: 120px;">
                        <button type="button" class="btn btn-outline-dark"
                                onclick="this.nextElementSibling.stepDown(); this.form.submit();">−</button>
                        <input type="number" name="qty" value="{{ $item->qty }}" min="1"
                               max="{{ $item->produk->stok ?? 1 }}"
                               class="form-control text-center border-dark"
                               style="max-width: 70px;"
                               onchange="this.form.submit()">
                        <button type="button" class="btn btn-outline-dark"
                                onclick="this.previousElementSibling.stepUp(); this.form.submit();">+</button>
                      </div>
                    </form>
                  </td>

                  <!-- Total -->
                  <td class="text-center fw-bold text-gold">
                    Rp {{ number_format($item->qty * (optional($item->produk)->harga ?? 0), 0, ',', '.') }}
                  </td>

                  <!-- Hapus -->
                  <td class="text-center">
                    <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Hapus produk dari keranjang?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="fa-solid fa-trash-can me-1"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Total Harga & Tombol Checkout --}}
      @php
        $totalHarga = $cartItems->sum(fn($item) => $item->qty * (optional($item->produk)->harga ?? 0));
      @endphp

      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
        <h5 class="fw-bold text-white mb-0">
          Total:
          <span class="text-gold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
        </h5>
        <a href="{{ route('checkout.index') }}" 
           class="btn btn-warning btn-lg rounded-pill px-4 fw-semibold text-dark shadow-sm">
          <i class="fa-solid fa-cash-register me-2"></i> Checkout
        </a>
      </div>

    @else
      {{-- Jika keranjang kosong --}}
      <div class="text-center py-5 text-white">
        <i class="fa-solid fa-cart-shopping fa-3x mb-3 text-gold"></i>
        <h5 class="fw-semibold mb-2">Keranjang Anda kosong</h5>
        <p class="opacity-75 mb-4">Yuk, mulai belanja produk Batik Wistara!</p>
        <a href="{{ url('/katalog') }}" class="btn btn-gold rounded-pill px-4 fw-semibold text-dark shadow-sm">
          <i class="fa-solid fa-store me-2"></i> Lihat Katalog
        </a>
      </div>
    @endif
  </div>
</section>

@include('inc.footer')
