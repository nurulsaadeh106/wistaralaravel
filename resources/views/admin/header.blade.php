<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Batik Wistara Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/header.css') }}">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-admin">
        <div class="container-fluid">

            <!-- LOGO -->
            <a class="navbar-brand" href="{{ url('/admin/dashboard') }}">
                <img src="{{ asset('img/logoputih.png') }}" alt="Batik Wistara Logo">
            </a>

            <!-- TOGGLE MOBILE -->
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <!-- MENU TENGAH -->
            <div class="collapse navbar-collapse justify-content-center" id="adminNavbar">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}"
                            href="{{ url('/admin/dashboard') }}">
                            BERANDA
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/kategori') ? 'active' : '' }}"
                            href="{{ url('/admin/kategori') }}">
                            KATEGORI PRODUK
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/produk') ? 'active' : '' }}"
                            href="{{ url('/admin/produk') }}">
                            PRODUK
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/berita') ? 'active' : '' }}"
                            href="{{ url('/admin/berita') }}">
                            BERITA
                        </a>
                    </li>
                </ul>
            </div>

            <!-- KANAN -->
            <div class="d-flex align-items-center">
                <!-- TROLI -->
                <a href="{{ url('/admin/pesanan') }}" class="icon-btn position-relative">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="badge">{{ $jumlahPesanan ?? 0 }}</span>
                </a>

                <!-- DROPDOWN ADMIN -->
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user me-2"></i>
                        {{ session('admin_name') ?? 'Super Admin' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item text-danger d-flex align-items-center"
                                href="{{ route('admin.logout') }}">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </nav>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
