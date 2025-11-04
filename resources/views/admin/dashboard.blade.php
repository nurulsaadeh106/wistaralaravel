@include('admin.header')

<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">

<!-- ===== HERO SECTION ===== -->
<section class="hero-admin">
    <!-- Background Video -->
    <video autoplay muted loop playsinline class="background-video">
        <source src="{{ asset('img/vidbatik.mp4') }}" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="hero-content">
        <h1><i class="fa-solid fa-chart-line me-2 text-warning"></i> Dashboard Admin</h1>
        <p>Selamat datang Admin <span class="text-warning">Batik Wistara</span></p>
    </div>
</section>

<!-- ===== MAIN DASHBOARD CONTENT ===== -->
<section class="dashboard-main">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <div class="icon-badge"><i class="fa-solid fa-layer-group"></i></div>
                    <h4>Kategori Produk</h4>
                    <p>Kelola kategori produk Batik Wistara & tambahkan koleksi baru.</p>
                    <a href="{{ url('/admin/kategori') }}" class="btn dashboard-btn w-100">Kelola Kategori</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <div class="icon-badge"><i class="fa-solid fa-shirt"></i></div>
                    <h4>Produk</h4>
                    <p>Kelola katalog produk Batik Wistara, tambah dan ubah koleksi.</p>
                    <a href="{{ url('/admin/produk') }}" class="btn dashboard-btn w-100">Kelola Produk</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <div class="icon-badge"><i class="fa-solid fa-box"></i></div>
                    <h4>Pesanan</h4>
                    <p>Kelola transaksi & pantau status pesanan pelanggan.</p>
                    <a href="{{ url('/admin/pesanan') }}" class="btn dashboard-btn w-100">Kelola Pesanan</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <div class="icon-badge"><i class="fa-solid fa-newspaper"></i></div>
                    <h4>Berita</h4>
                    <p>Posting informasi & artikel terbaru untuk pengguna.</p>
                    <a href="{{ url('/admin/berita') }}" class="btn dashboard-btn w-100">Kelola Berita</a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ===== FOOTER DECORATION ===== -->
<div class="footer-image"></div>

@include('admin.footer')
