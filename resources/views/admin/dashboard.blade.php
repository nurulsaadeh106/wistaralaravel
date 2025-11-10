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

<!-- ===== SALES ANALYTICS SECTION ===== -->
<section class="dashboard-analytics">
    <div class="container">
        <div class="analytics-card">
            <div class="analytics-header">
                <div>
                    <h3 class="mb-1">Monitoring Penjualan</h3>
                    <p class="text-muted mb-0">Ringkasan transaksi 12 bulan terakhir / klik untuk detail produk</p>
                </div>
                <div class="quarter-nav">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="quarter-prev-btn">
                        <i class="fa-solid fa-chevron-left me-1"></i> Quartal Sebelumnya
                    </button>
                    <span class="quarter-label text-muted mx-2" id="quarter-label">-</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="quarter-next-btn">
                        Quartal Berikutnya <i class="fa-solid fa-chevron-right ms-1"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-outline-dark btn-sm" id="sales-back-btn" hidden>Level Bulanan</button>
            </div>
            <div id="sales-chart" class="analytics-chart"></div>
            <div class="analytics-legend mt-3">
                <small class="text-muted">Tips: klik salah satu batang untuk drill-down ke penjualan per produk.</small>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER DECORATION ===== -->
<div class="footer-image"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartElement = document.querySelector('#sales-chart');
        const backButton = document.querySelector('#sales-back-btn');

        if (!chartElement || !backButton) {
            return;
        }

        const currencyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        });

        let analyticsData = null;
        let currentLevel = 'summary';
        let productList = [];
        let productLabels = [];
        let quarterSlices = [];
        let currentQuarterIndex = 0;
        let visibleSummaryItems = [];

        const chart = new ApexCharts(chartElement, {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: { show: false },
                animations: { easing: 'easeinout', speed: 500 },
                events: {
                    dataPointSelection(event, chartContext, config) {
                        if (!visibleSummaryItems.length || currentLevel !== 'summary') {
                            return;
                        }

                        const clickedIndex = config.dataPointIndex;
                        const selected = visibleSummaryItems[clickedIndex];

                        if (selected) {
                            renderDetail(selected.key, selected.label);
                        }
                    }
                }
            },
            colors: ['#d4af37'],
            plotOptions: {
                bar: {
                    distributed: false,
                    columnWidth: '45%',
                    borderRadius: 8
                }
            },
            series: [],
            xaxis: {
                categories: [],
                labels: {
                    style: { fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    formatter: (value) => currencyFormatter.format(value)
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (value) => currencyFormatter.format(value)
            },
            tooltip: {
                y: {
                    formatter: (value) => currencyFormatter.format(value)
                }
            },
            noData: {
                text: 'Memuat data penjualan...',
                align: 'center',
                style: {
                    fontSize: '14px',
                    color: '#071739'
                }
            }
        });

        chart.render();

        const applyCurrencyFormatting = (maxValue = 0) => {
            const baseMax = 1000000; // default 5 juta
            const step = 250000;
            const targetMax = Math.max(maxValue, step);
            const safeMax = Math.max(baseMax, Math.ceil(targetMax / step) * step);
            const tickAmount = Math.max(Math.round(safeMax / step), 1);

            chart.updateOptions({
                yaxis: {
                    min: 0,
                    max: safeMax,
                    tickAmount,
                    labels: {
                        formatter: (value) => currencyFormatter.format(value)
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (value) => currencyFormatter.format(value)
                },
                tooltip: {
                    y: {
                        formatter: (value) => currencyFormatter.format(value)
                    }
                }
            }, false, true);
        };

        const applyQuantityFormatting = (maxValue = 0) => {
            const step = 1;
            const targetMax = Math.max(maxValue, step);
            const safeMax = Math.max(10, Math.ceil(targetMax / step) * step);
            const tickAmount = Math.max(Math.round(safeMax / step), 1);

            chart.updateOptions({
                yaxis: {
                    min: 0,
                    max: safeMax,
                    tickAmount,
                    labels: {
                        formatter: (value) => `${value}`
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (value) => `${value}`
                },
                tooltip: {
                    y: {
                        formatter: (value) => `${value} pcs`
                    }
                }
            }, false, true);
        };

        const quarterPrevBtn = document.querySelector('#quarter-prev-btn');
        const quarterNextBtn = document.querySelector('#quarter-next-btn');
        const quarterLabel = document.querySelector('#quarter-label');

        function prepareQuarterSlices(summary = []) {
            if (!Array.isArray(summary) || !summary.length) {
                return [];
            }

            const sorted = [...summary].sort((a, b) => {
                return new Date(a.key + '-01') - new Date(b.key + '-01');
            });

            const slices = [];
            for (let i = 0; i < sorted.length; i += 3) {
                const chunk = sorted.slice(i, i + 3);
                if (!chunk.length) continue;
                const quarterLabel = buildQuarterLabel(chunk[0]);
                slices.push({
                    label: quarterLabel,
                    items: chunk,
                });
            }
            return slices;
        }

        function buildQuarterLabel(item) {
            if (!item || !item.key) return '-';
            const [year, month] = item.key.split('-');
            const quarter = Math.floor((parseInt(month, 10) - 1) / 3) + 1;
            return `Q${quarter} ${year}`;
        }

        function updateQuarterNav() {
            if (!quarterSlices.length) {
                quarterLabel.textContent = '-';
                quarterPrevBtn.disabled = true;
                quarterNextBtn.disabled = true;
                return;
            }
            quarterLabel.textContent = quarterSlices[currentQuarterIndex]?.label ?? '-';
            quarterPrevBtn.disabled = currentQuarterIndex === 0;
            quarterNextBtn.disabled = currentQuarterIndex === quarterSlices.length - 1;
        }

        fetch('{{ route('admin.sales.data') }}')
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data penjualan');
                }
                return response.json();
            })
            .then((data) => {
                analyticsData = data;
                productList = Array.isArray(data.products) ? data.products : [];
                productLabels = productList.map((product) => product.name);
                quarterSlices = prepareQuarterSlices(data.summary || []);
                currentQuarterIndex = quarterSlices.length ? quarterSlices.length - 1 : 0;
                renderSummary();
            })
            .catch((error) => {
                console.error(error);
                chart.updateOptions({
                    noData: { text: error.message || 'Data penjualan gagal dimuat' }
                });
            });

        backButton.addEventListener('click', () => {
            renderSummary();
        });

        quarterPrevBtn.addEventListener('click', () => {
            if (currentQuarterIndex > 0) {
                currentQuarterIndex -= 1;
                renderSummary();
            }
        });

        quarterNextBtn.addEventListener('click', () => {
            if (currentQuarterIndex < quarterSlices.length - 1) {
                currentQuarterIndex += 1;
                renderSummary();
            }
        });

        function renderSummary() {
            if (!quarterSlices.length) {
                chart.updateSeries([]);
                chart.updateOptions({
                    noData: { text: 'Belum ada data penjualan' }
                });
                visibleSummaryItems = [];
                updateQuarterNav();
                return;
            }

            currentLevel = 'summary';
            backButton.hidden = true;

            const activeSlice = quarterSlices[currentQuarterIndex] || { items: [] };
            visibleSummaryItems = activeSlice.items;

            const labels = activeSlice.items.map((item) => item.label);
            const totals = activeSlice.items.map((item) => item.total);
            const maxTotal = totals.reduce((acc, value) => Math.max(acc, value), 0);

            chart.updateOptions({
                title: {
                    text: 'Total Penjualan Bulanan',
                    style: { fontSize: '18px' }
                },
                subtitle: {
                    text: 'Klik batang bulan tertentu untuk melihat penjualan per produk',
                    style: { fontSize: '13px' }
                },
                xaxis: { categories: labels }
            });

            chart.updateSeries([
                {
                    name: 'Total Penjualan',
                    data: totals
                }
            ]);

            applyCurrencyFormatting(maxTotal);
            updateQuarterNav();
        }

        function renderDetail(monthKey, label) {
            currentLevel = 'detail';
            backButton.hidden = false;

            const detail =
                analyticsData && analyticsData.details && analyticsData.details[monthKey]
                    ? analyticsData.details[monthKey]
                    : [];

            if (!detail.length) {
                applyQuantityFormatting(0);
                chart.updateSeries([]);
                chart.updateOptions({
                    title: { text: `Detail Penjualan (${label})` },
                    subtitle: { text: 'Belum ada data produk pada bulan ini' },
                    noData: { text: 'Detail produk belum tersedia' }
                });
                return;
            }

            chart.updateOptions({
                title: { text: `Penjualan per Produk (${label})` },
                subtitle: { text: 'Tekan "Level Bulanan" untuk kembali' },
                xaxis: { categories: detail.map((item) => item.name) }
            });

            chart.updateSeries([
                {
                    name: 'Qty Terjual',
                    data: detail.map((item) => item.qty)
                }
            ]);

            const maxQty = detail.reduce((acc, item) => Math.max(acc, item.qty), 0);
            applyQuantityFormatting(maxQty);
        }
    });
</script>

@include('admin.footer')
