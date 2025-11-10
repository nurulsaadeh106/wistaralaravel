{{-- ==================== Header & Navbar ==================== --}}
@include('inc.header')

<!-- ================= HERO SECTION ================= -->
<section class="katalog-hero d-flex align-items-center justify-content-center position-relative">
  <div class="hero-overlay"></div>
  <div class="container text-center text-light position-relative z-2">
    <h1 class="display-4 fw-bold mb-2">Katalog Produk</h1>
    <p class="lead opacity-75">Temukan keindahan batik terbaik khas Wistara</p>
  </div>
</section>

<!-- ================= FILTER KATEGORI ================= -->
<section class="filter-section py-4 bg-white shadow-sm position-relative z-2">
  <div class="container">
    <div class="d-flex justify-content-center flex-wrap gap-2 filter-pills">
      <a href="{{ route('katalog', ['kategori' => 'all']) }}"
         class="btn kategori-pill {{ $filter === 'all' ? 'active' : '' }}">
         Semua
      </a>
      @foreach($kategori as $k)
        <a href="{{ route('katalog', ['kategori' => $k->id_kategori]) }}"
           class="btn kategori-pill {{ (string)$filter === (string)$k->id_kategori ? 'active' : '' }}">
           {{ $k->nama_kategori }}
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- ================= KATALOG PRODUK ================= -->
<section class="section-katalog py-5 bg-light">
  <div class="container">
    <div class="row g-4">
      @foreach($produk as $p)
        @php
          $fileName = basename($p->gambar);
          $gambarPath = public_path('uploads/produk/'.$fileName);
          $gambarUrl = (file_exists($gambarPath) && $fileName)
              ? asset('uploads/produk/'.$fileName)
              : asset('img/no-image.jpg');
        @endphp

        <div class="col-12 col-sm-6 col-lg-4">
          <div class="produk-card border-0 rounded-4 shadow-sm h-100 d-flex flex-column overflow-hidden bg-white">
            
            <!-- Gambar Produk -->
            <div class="position-relative" style="height: 240px;">
              <img src="{{ $gambarUrl }}" 
                  alt="{{ $p->nama_produk }}" 
                  class="w-100 h-100 object-fit-cover rounded-top-4">
              <span class="badge bg-light text-gold position-absolute top-0 end-0 m-2 px-3 py-2 rounded-pill">
                {{ $p->nama_kategori }}
              </span>
            </div>

            <!-- Body Produk -->
            <div class="p-3 d-flex flex-column flex-grow-1">
              <h5 class="fw-bold mb-1 text-dark text-truncate">{{ $p->nama_produk }}</h5>
              <p class="text-gold fw-bold mb-2">
                Rp {{ number_format($p->harga, 0, ',', '.') }}
              </p>
              <p class="text-muted small flex-grow-1 mb-3">
                {{ Str::limit($p->deskripsi, 70) }}
              </p>
              <button type="button" 
                      class="btn btn-gold w-100 rounded-pill fw-semibold mt-auto"
                      data-bs-toggle="modal" 
                      data-bs-target="#produkModal{{ $p->id_produk }}">
                <i class="bi bi-eye me-1"></i> Detail Produk
              </button>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- ============= Modal Detail Produk & Qty ============= -->
  @foreach($produk as $p)
  @php
    $fileName = basename($p->gambar);
    $gambarPath = public_path('uploads/produk/'.$fileName);
    $gambarUrl = (file_exists($gambarPath) && $fileName)
        ? asset('uploads/produk/'.$fileName)
        : asset('img/no-image.jpg');
  @endphp

  <!-- Modal Produk -->
  <div class="modal fade" id="produkModal{{ $p->id_produk }}" tabindex="-1" aria-labelledby="produkModalLabel{{ $p->id_produk }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

        <!-- HEADER -->
        <div class="modal-header border-0 bg-dark text-light py-3 px-4">
          <h5 class="modal-title fw-bold">{{ $p->nama_produk }}</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body p-4">
          <div class="row g-4 align-items-start">

            <!-- Gambar Produk -->
            <div class="col-md-5 text-center">
              <img src="{{ $gambarUrl }}" class="img-fluid rounded-4 shadow-sm border" alt="{{ $p->nama_produk }}">
            </div>

            <!-- Info Produk -->
            <div class="col-md-7">
              <span class="badge bg-secondary mb-2">{{ $p->nama_kategori }}</span>
              <h4 class="fw-bold text-warning mb-3">
                Rp {{ number_format($p->harga, 0, ',', '.') }}
              </h4>
              <p class="mb-4 text-muted" style="line-height: 1.7;">
                {!! nl2br(e($p->deskripsi)) !!}
              </p>

              <!-- Tombol Aksi -->
              <div class="d-flex flex-column flex-md-row gap-2 mb-3">
                <!-- Beli Sekarang -->
                <a href="{{ route('checkout.direct', $p->id_produk) }}"
                  class="btn btn-dark flex-fill fw-semibold py-2">
                  🛍️ Beli Sekarang
                </a>

                @auth
                <!-- Tombol Icon Keranjang -->
                <button type="button"
                        class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px; padding: 0;"
                        data-bs-toggle="modal"
                        data-bs-target="#qtyModal{{ $p->id_produk }}">
                  <i class="bi bi-cart-plus fs-5"></i>
                </button>
                @else
                <a href="{{ route('login') }}"
                  class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                  style="width: 45px; height: 45px; padding: 0;">
                  <i class="bi bi-cart-plus fs-5"></i>
                </a>
                @endauth
              </div>

              <hr class="my-3">

              <!-- Channel Lain -->
              <p class="fw-semibold mb-2">Atau beli melalui:</p>
              <div class="d-flex flex-wrap gap-2">
                <a href="https://wa.me/62895381110035?text={{ urlencode('Halo admin, saya tertarik dengan produk '.$p->nama_produk) }}"
                  target="_blank" class="btn btn-outline-success btn-sm d-flex align-items-center gap-2">
                  <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
                @if($p->link_shopee)
                <a href="{{ $p->link_shopee }}" target="_blank" class="btn btn-outline-warning btn-sm d-flex align-items-center gap-2">
                  <i class="bi bi-bag"></i> Shopee
                </a>
                @endif
                @if($p->link_tiktok)
                <a href="{{ $p->link_tiktok }}" target="_blank" class="btn btn-outline-dark btn-sm d-flex align-items-center gap-2">
                  <i class="bi bi-tiktok"></i> TikTokShop
                </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Qty Kecil -->
  @auth
  <div class="modal fade" id="qtyModal{{ $p->id_produk }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content border-0 rounded-4 shadow-lg">
        <div class="modal-header bg-dark text-white py-2">
          <h6 class="modal-title fw-semibold">Jumlah ke Keranjang</h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('cart.add', $p->id_produk) }}" method="POST">
          @csrf
          <div class="modal-body text-center">
            <input type="number" name="qty" value="1" min="1" max="{{ $p->stok }}" 
                  class="form-control text-center mb-3 mx-auto" style="max-width: 100px;">
            <button type="submit" class="btn btn-dark w-100 rounded-pill">
              <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endauth
  @endforeach
</section>

{{-- ==================== Footer ==================== --}}
@include('inc.footer')
