@include('inc.header')

<!-- ================= HERO SECTION ================= -->
<section class="hero-section d-flex justify-content-center align-items-center text-center text-light">
    <!-- Background Video -->
    <video autoplay muted loop playsinline class="background-video">
        <source src="{{ asset('img/vidbatik.mp4') }}" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <!-- Overlay -->
    <div class="overlay"></div>

    <!-- Content -->
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="200">
        <img src="{{ asset('img/logoputih.png') }}" alt="Batik Wistara" class="hero-logo mb-4">
        <h1 class="animate-title">Selamat Datang di <span class="text-warning">Batik Wistara</span></h1>
        <p class="animate-subtitle">Batik Wistara menghadirkan karya batik berkualitas tinggi, memadukan keindahan
            budaya dan sentuhan modern.</p>
        <a href="{{ url('/katalog') }}" class="btn btn-hero mt-3 px-4" data-aos="zoom-in" data-aos-delay="400">Lihat
            Katalog</a>
    </div>
</section>

<!-- ================= TENTANG KAMI ================= -->
<section class="section-about py-5">
    <div class="container py-4">
        <div class="row align-items-center g-5 flex-column-reverse flex-lg-row">

            <!-- TEKS -->
            <div class="col-lg-6 about-textbox text-center text-lg-start" data-aos="fade-right"
                data-aos-duration="1000">
                <h2 class="about-title fw-bold mb-4">Tentang Kami</h2>
                <p class="about-paragraph">
                    Sejak awal berdiri, <strong>Batik Wistara</strong> berkomitmen menjaga warisan batik Nusantara
                    melalui desain yang autentik dan kualitas premium.
                    Setiap helai kain Batik Wistara menghadirkan harmoni antara tradisi dan inovasi — melestarikan
                    budaya dengan sentuhan modern.
                </p>
                <a href="{{ url('/tentang') }}" class="about-button mt-3">Selengkapnya</a>
            </div>

            <!-- GAMBAR -->
            <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-duration="1000">
                <img src="{{ asset('img/about.jpg') }}" alt="Tentang Batik Wistara"
                    class="about-image rounded-4 shadow-lg img-fluid">
            </div>

        </div>
    </div>
</section>

<!-- ================= KATALOG PRODUK SLIDER ================= -->
<section class="section-katalog-slider py-5">
    <div class="container">

        <!-- Judul -->
        <div class="text-center mb-5" data-aos="fade-up" data-aos-duration="1000">
            <h2 class="fw-bold text-gold">Katalog Wistara</h2>
            <hr class="mx-auto mt-3" style="width: 100px; height: 3px; background-color: #ffffffff;">
        </div>

        <div class="row g-4 align-items-center">

            <!-- KIRI: Video -->
            <div class="col-lg-5 d-flex justify-content-center" data-aos="fade-right" data-aos-duration="1000">
                <div class="katalog-media overflow-hidden rounded-4 shadow">
                    <video autoplay muted loop playsinline>
                        <source src="{{ asset('img/vidbatik.mp4') }}" type="video/mp4">
                        Browser Anda tidak mendukung video.
                    </video>
                </div>
            </div>

            <!-- KANAN: Carousel Produk -->
            <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div id="carouselKatalog" class="carousel slide mb-4" data-bs-interval="0">
                    <div class="carousel-inner">

                        @foreach ($produk->chunk(3) as $chunkIndex => $chunk)
                            <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                <div class="row g-4 justify-content-center">

                                    @foreach ($chunk as $p)
                                        @php
                                            $path = public_path($p->gambar);
                                            $gambarUrl =
                                                file_exists($path) && $p->gambar
                                                    ? asset($p->gambar)
                                                    : asset('img/no-image.jpg');
                                        @endphp

                                        <div class="col-12 col-sm-6 col-md-4" data-aos="zoom-in"
                                            data-aos-duration="800">
                                            <div class="card produk-card h-100 border-0 shadow-sm">

                                                <!-- Gambar Produk -->
                                                <div class="produk-img-wrapper position-relative">
                                                    <img src="{{ $gambarUrl }}" alt="{{ $p->nama_produk }}"
                                                        class="card-img-top produk-img">

                                                    <!-- Badge kategori -->
                                                    <span
                                                        class="kategori-badge badge bg-light text-gold position-absolute top-0 start-0 m-2">
                                                        {{ $p->nama_kategori }}
                                                    </span>
                                                </div>

                                                <!-- Body Produk -->
                                                <div class="card-body d-flex flex-column">
                                                    <h6 class="fw-bold mb-1 text-dark text-truncate">
                                                        {{ $p->nama_produk }}</h6>

                                                    <!-- Harga Produk -->
                                                    <p class="harga-produk mb-2 fw-bold text-warning">
                                                        Rp {{ number_format($p->harga, 0, ',', '.') }}
                                                    </p>

                                                    <p class="text-muted small flex-grow-1 mb-3">
                                                        {{ Str::limit(strip_tags($p->deskripsi), 60) }}
                                                    </p>

                                                    <button type="button"
                                                        class="btn btn-outline-dark btn-sm mt-auto w-100 fw-semibold"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#produkModal{{ $p->id_produk }}">
                                                        Detail Produk
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endforeach

                    </div>

                    <!-- Navigasi Carousel -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselKatalog"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"></span>
                        <span class="visually-hidden">Sebelumnya</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselKatalog"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-2"></span>
                        <span class="visually-hidden">Selanjutnya</span>
                    </button>
                </div>

                <!-- Tombol ke Katalog -->
                <div class="text-center mt-3" data-aos="fade-up" data-aos-delay="300">
                    <a href="{{ url('/katalog') }}" class="btn btn-hero px-4 py-2">
                        Lihat Semua Produk
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Modal Detail Produk ===== -->
    @foreach ($produk as $p)
        @php
            $path = public_path($p->gambar);
            $gambarUrl = file_exists($path) && $p->gambar ? asset($p->gambar) : asset('img/no-image.jpg');
        @endphp

        <!-- Modal Produk -->
        <div class="modal fade" id="produkModal{{ $p->id_produk }}" tabindex="-1"
            aria-labelledby="produkModalLabel{{ $p->id_produk }}" aria-hidden="true">
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
                                <img src="{{ $gambarUrl }}" class="img-fluid rounded-4 shadow-sm border"
                                    alt="{{ $p->nama_produk }}">
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
                                    <a href="{{ route('checkout', $p->id_produk) }}"
                                    class="btn btn-dark flex-fill fw-semibold py-2">
                                    🛍️ Beli Sekarang
                                    </a>

                                    @auth
                                        <!-- Tombol Icon Keranjang Kecil -->
                                        <button type="button"
                                            class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                                            style="width: 45px; height: 45px; padding: 0;" data-bs-toggle="modal"
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
                                    <a href="https://wa.me/62895381110035?text={{ urlencode('Halo admin, saya tertarik dengan produk ' . $p->nama_produk) }}"
                                        target="_blank"
                                        class="btn btn-outline-success btn-sm d-flex align-items-center gap-2">
                                        <i class="bi bi-whatsapp"></i> WhatsApp
                                    </a>
                                    @if ($p->link_shopee)
                                        <a href="{{ $p->link_shopee }}" target="_blank"
                                            class="btn btn-outline-warning btn-sm d-flex align-items-center gap-2">
                                            <i class="bi bi-bag"></i> Shopee
                                        </a>
                                    @endif
                                    @if ($p->link_tiktok)
                                        <a href="{{ $p->link_tiktok }}" target="_blank"
                                            class="btn btn-outline-dark btn-sm d-flex align-items-center gap-2">
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


        <!-- ===== Modal Qty Kecil ===== -->
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
                                <input type="number" name="qty" value="1" min="1"
                                    max="{{ $p->stok }}" class="form-control text-center mb-3 mx-auto"
                                    style="max-width: 100px;">
                                <button type="submit" class="btn btn-dark w-100">
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

<!-- ================= BERITA TERKINI ================= -->
<section class="section-berita py-5">
    <div class="container-berita">
        <!-- Judul -->
        <div class="text-center mb-4" data-aos="fade-up" data-aos-duration="1000">
            <h2 class="text-gold">Berita Terkini</h2>
            <hr class="berita-divider mx-auto">
        </div>

        <!-- Grid Berita -->
        <div class="berita-grid">
            @foreach ($berita as $index => $b)
                <div class="berita-card" data-aos="fade-up" data-aos-duration="1000"
                    data-aos-delay="{{ $index * 150 }}">

                    <!-- Gambar + Sumber Overlay -->
                    <div class="berita-img-wrapper position-relative">
                        @if (filter_var($b->gambar, FILTER_VALIDATE_URL))
                            <img src="{{ $b->gambar }}" alt="{{ $b->judul }}">
                        @else
                            <img src="{{ asset($b->gambar) }}" alt="{{ $b->judul }}">
                        @endif

                        @if (!empty($b->sumber))
                            <div class="berita-sumber-overlay">
                                Sumber:
                                @if (!empty($b->tautan_sumber))
                                    <a href="{{ $b->tautan_sumber }}" target="_blank">{{ $b->sumber }}</a>
                                @else
                                    {{ $b->sumber }}
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Judul & Konten Singkat -->
                    <h3 class="berita-judul mt-3">{{ $b->judul }}</h3>
                    <p class="berita-deskripsi">{{ Str::limit(strip_tags($b->konten), 150) }}</p>

                    <!-- Tanggal -->
                    <p class="berita-tanggal text-muted mb-2">
                        <small>{{ \Carbon\Carbon::parse($b->tanggal)->format('d M Y') }}</small>
                    </p>

                    <!-- Link -->
                    @if (!empty($b->tautan_sumber))
                        <a href="{{ $b->tautan_sumber }}" target="_blank" class="berita-link">
                            Baca Selengkapnya →
                        </a>
                    @else
                        <a href="{{ route('berita.detail', $b->slug) }}" class="berita-link">
                            Baca Selengkapnya →
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Tombol Lihat Semua -->
        <div class="berita-footer mt-4 text-center" data-aos="fade-up" data-aos-delay="200">
            <a href="{{ url('/berita') }}" class="btn-lihat-semua">Lihat Semua Berita</a>
        </div>
    </div>
</section>

@include('inc.footer')
