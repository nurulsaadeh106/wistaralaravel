@include('admin.header')

<link rel="stylesheet" href="{{ asset('css/admin/order.css') }}">

<style>
    /* 🌄 Background fullscreen */
    body {
        font-family: 'Poppins', sans-serif;
        color: #0b1841;
        margin: 0;
        min-height: 100vh;
        background: url('{{ asset('img/background1.svg') }}') no-repeat center center fixed;
        background-size: cover;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="fw-bold text-dark border-bottom pb-2">Kelola Pesanan</h2>
    </div>

    <!-- 🔍 Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3 align-items-center" method="GET"
                action="{{ route('admin.orders.index') }}">
                <div class="col-md-auto">
                    <label class="form-label mb-1 fw-semibold text-secondary">Dari</label>
                    <input type="date" name="start" value="{{ request('start') }}"
                        class="form-control border-0 shadow-sm" onchange="this.form.submit()">
                </div>
                <div class="col-md-auto">
                    <label class="form-label mb-1 fw-semibold text-secondary">Sampai</label>
                    <input type="date" name="end" value="{{ request('end') }}"
                        class="form-control border-0 shadow-sm" onchange="this.form.submit()">
                </div>
                <div class="col-md">
                    <label class="form-label mb-1 fw-semibold text-secondary">Kata Kunci</label>
                    <input type="text" name="keyword" placeholder="Cari nama atau ID pesanan..."
                        value="{{ request('keyword') }}" class="form-control border-0 shadow-sm"
                        onkeypress="if(event.key === 'Enter'){ this.form.submit(); }">
                </div>
            </form>
        </div>
    </div>

    <!-- 🗂️ Tabs -->
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header py-3" style="background-color: #001f3f;">
            <ul class="nav nav-tabs card-header-tabs justify-content-center" id="orderTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active fw-semibold" data-bs-toggle="tab"
                        data-bs-target="#Semua" type="button">📋 Semua</button></li>
                <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#pending"
                        type="button">🕒 Pending</button></li>
                <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#proses"
                        type="button">⏳ Proses</button></li>
                <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#selesai"
                        type="button">✅ Selesai</button></li>
                <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#batal"
                        type="button">❌ Batal</button></li>
            </ul>
        </div>

        <div class="tab-content p-4 bg-white">
            @php
                $statusList = [
                    'Semua' => null,
                    'pending' => 'pending',
                    'proses' => 'proses',
                    'selesai' => 'selesai',
                    'batal' => 'batal',
                ];
            @endphp

            @foreach ($statusList as $key => $status)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $key }}">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle text-center m-0 shadow-sm border">
                            <thead class="table-header-navy">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status Pesanan</th>
                                    <th>Status Pembayaran</th>
                                    <th>Bukti</th>
                                    <th>Tipe</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($orders->where('status', $status)->when($status == null, fn($q) => $orders) as $order)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->nama }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>

                                        <!-- 🔵 STATUS PESANAN -->
                                        @php
                                            if (!function_exists('getStatusClass')) {
                                                function getStatusClass($status)
                                                {
                                                    return match ($status) {
                                                        'pending' => 'text-bg-secondary',
                                                        'proses' => 'text-bg-warning',
                                                        'selesai' => 'text-bg-success',
                                                        'batal' => 'text-bg-danger',
                                                        default => 'text-bg-primary',
                                                    };
                                                }
                                            }
                                        @endphp

                                        <td>
                                            <form id="statusForm{{ $order->id }}"
                                                action="{{ route('admin.orders.updateStatus', $order->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status"
                                                    id="statusInput{{ $order->id }}" value="{{ $order->status }}">

                                                <div class="dropdown">
                                                    <button id="statusBadge{{ $order->id }}"
                                                        class="badge dropdown-toggle {{ getStatusClass($order->status) }}"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        {{ ucfirst($order->status) }}
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach (['pending', 'proses', 'selesai', 'batal'] as $status)
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="updateOrder('{{ $order->id }}', '{{ $status }}')">
                                                                    {{ ucfirst($status) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </form>
                                        </td>


                                        <!-- 💰 STATUS PEMBAYARAN -->
                                        @php
                                            if (!function_exists('getPaymentClass')) {
                                                function getPaymentClass($status)
                                                {
                                                    return match ($status) {
                                                        'belum_bayar' => 'text-bg-secondary',
                                                        'menunggu_verifikasi' => 'text-bg-warning',
                                                        'lunas' => 'text-bg-success',
                                                        'gagal' => 'text-bg-danger',
                                                        default => 'text-bg-primary',
                                                    };
                                                }
                                            }
                                        @endphp

                                        <td>
                                            <form id="paymentForm{{ $order->id }}"
                                                action="{{ route('admin.orders.updatePayment', $order->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status_pembayaran"
                                                    id="paymentInput{{ $order->id }}"
                                                    value="{{ $order->status_pembayaran }}">

                                                <div class="dropdown">
                                                    <button id="paymentBadge{{ $order->id }}"
                                                        class="badge dropdown-toggle {{ getPaymentClass($order->status_pembayaran) }}"
                                                        type="button" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        {{ ucwords(str_replace('_', ' ', $order->status_pembayaran)) }}
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach (['belum_bayar', 'menunggu_verifikasi', 'lunas', 'gagal'] as $status)
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    onclick="updatePayment('{{ $order->id }}', '{{ $status }}')">
                                                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </form>
                                        </td>

                                        <!-- 🧾 Bukti -->
                                        <td>
                                            @if ($order->bukti_pembayaran && file_exists(public_path('uploads/bukti/' . $order->bukti_pembayaran)))
                                                <button class="btn btn-sm btn-primary-navy rounded-pill"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#buktiModal{{ $order->id }}">
                                                    <i class="fa-solid fa-image me-1"></i> Lihat
                                                </button>

                                                <!-- Modal Bukti Pembayaran TANPA OVERLAY -->
                                                <div class="modal fade" id="buktiModal{{ $order->id }}"
                                                    tabindex="-1" aria-hidden="true" data-bs-backdrop="false"
                                                    {{-- ❌ overlay dimatikan --}} data-bs-keyboard="true">
                                                    {{-- ✅ ESC bisa menutup modal --}}
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content shadow-lg border-0">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Bukti Pembayaran
                                                                    #{{ $order->id }}</h5>
                                                                <button type="button"
                                                                    class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset('uploads/bukti/' . $order->bukti_pembayaran) }}"
                                                                    alt="Bukti Pembayaran {{ $order->id }}"
                                                                    class="img-fluid rounded shadow-sm"
                                                                    style="max-height: 80vh; object-fit: contain;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>{{ ucfirst($order->tipe_order) }}</td>

                                        <!-- 🧭 Aksi -->
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-primary-navy rounded-pill px-3">
                                                <i class="fa-solid fa-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10" class="text-center py-2 text-muted">
                                        Total Pesanan:
                                        {{ $orders->where('status', $status)->when($status == null, fn($q) => $orders)->count() }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="{{ asset('js/order.js') }}"></script>

@include('admin.footer')
