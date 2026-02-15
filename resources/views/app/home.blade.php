@extends('app.layouts.main')

@section('title', 'Dashboard - Sakina Kantin')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    .home-hero {
        background: radial-gradient(circle at 10% 20%, rgba(255, 255, 255, .22), transparent 35%),
            linear-gradient(130deg, #dc7a3f 0%, #c7642e 52%, #b45324 100%);
        border-radius: .85rem;
        color: #fff;
        padding: 2rem 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 .55rem 1.25rem rgba(180, 83, 36, .28);
    }

    .home-hero::before,
    .home-hero::after {
        content: "";
        position: absolute;
        pointer-events: none;
        border-radius: 100%;
    }

    .home-hero::after {
        right: -46px;
        top: -56px;
        width: 190px;
        height: 190px;
        background: rgba(255, 255, 255, .15);
    }

    .home-hero::before {
        left: -55px;
        bottom: -70px;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, .11);
    }

    .home-stat {
        border: 0;
        border-radius: .75rem;
        box-shadow: 0 .25rem .9rem rgba(31, 41, 55, .08);
    }

    .home-stat .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .home-stat .meta {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 800;
        margin-bottom: .15rem;
    }

    .home-stat .value {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1f2937;
        line-height: 1.2;
    }

    .home-stat .icon-wrap {
        width: 2.4rem;
        height: 2.4rem;
        border-radius: .65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        color: #fff;
    }

    .home-actions {
        border-radius: .75rem;
    }

    .home-actions .btn {
        border-radius: .45rem;
        font-weight: 700;
        padding: .42rem .75rem;
        font-size: .83rem;
    }

    .home-actions .btn-home-main {
        background: linear-gradient(135deg, #c7642e 0%, #b45324 100%);
        border: 1px solid #9f4a20;
        color: #fff;
    }

    .home-actions .btn-home-main:hover {
        background: linear-gradient(135deg, #b45324 0%, #8f3f1b 100%);
        border-color: #7f3818;
        color: #fff;
    }

    .home-actions .btn-home-soft {
        background: #f8e7dc;
        border: 1px solid #dfb8a0;
        color: #7a3f1f;
    }

    .home-actions .btn-home-soft:hover {
        background: #f0d7c8;
        border-color: #c99679;
        color: #633217;
    }

    .home-chart-card {
        border-radius: .75rem;
        overflow: hidden;
    }

    .home-chart-card .card-header {
        background: linear-gradient(135deg, #a84f22 0%, #c7642e 100%) !important;
    }

    .chart-wrap {
        position: relative;
        min-height: 280px;
    }

    .home-feature {
        border: 0;
        border-radius: .75rem;
        box-shadow: 0 .2rem .9rem rgba(31, 41, 55, .07);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .home-feature:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1.2rem rgba(31, 41, 55, .12);
    }

    .home-feature .icon {
        font-size: 1.9rem;
        line-height: 1;
    }

    .home-brand-note {
        background: #f8e8dc;
        border: 1px solid #e4c3ad;
        color: #7a3f1f;
        border-radius: .65rem;
        font-size: .82rem;
        padding: .45rem .65rem;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-weight: 700;
    }

    @media (max-width: 992px) {
        .chart-wrap {
            min-height: 240px;
        }
    }
</style>
@endpush

@section('content')
<div class="home-hero mb-3">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="small text-uppercase fw-bold opacity-75 mb-2">Sakina Kantin</div>
            <h2 class="h3 fw-bold mb-2">Selamat Datang di Sakina Kantin</h2>
            <p class="mb-0">Pesan makanan favorit dengan mudah dan cepat.</p>
            <div class="home-brand-note mt-3"><i class="bi bi-stars"></i> Dashboard versi Laravel</div>
        </div>
        <div>
            <a class="btn btn-warning fw-bold text-dark" href="{{ url('/app/orders') }}">
                <i class="bi bi-basket me-1"></i>Mulai Order Sekarang
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card home-stat">
            <div class="card-body">
                <div>
                    <div class="meta text-primary">User</div>
                    <div class="value">{{ $user?->username ?? '-' }}</div>
                </div>
                <span class="icon-wrap bg-primary"><i class="bi bi-person-fill"></i></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card home-stat">
            <div class="card-body">
                <div>
                    <div class="meta text-success">Level</div>
                    <div class="value">{{ $user?->level ?? '-' }}</div>
                </div>
                <span class="icon-wrap bg-success"><i class="bi bi-award-fill"></i></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card home-stat">
            <div class="card-body">
                <div>
                    <div class="meta text-info">Kios</div>
                    <div class="value">{{ $user?->Kios ?? '-' }}</div>
                </div>
                <span class="icon-wrap bg-info"><i class="bi bi-shop"></i></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card home-stat">
            <div class="card-body">
                <div>
                    <div class="meta text-warning">Order Hari Ini</div>
                    <div class="value">{{ $openOrders }}</div>
                </div>
                <span class="icon-wrap bg-warning text-dark"><i class="bi bi-bag-check-fill"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="card home-actions">
    <div class="card-body d-flex flex-wrap gap-2">
        <a class="btn btn-home-main" href="{{ url('/app/menu') }}">Daftar Menu</a>
        <a class="btn btn-home-main" href="{{ url('/app/orders') }}">Transaksi Order</a>
        <a class="btn btn-home-main" href="{{ url('/app/reports/orders') }}">Laporan</a>
        @if((int) session('level_kantin') === 1)
            <a class="btn btn-home-soft" href="{{ url('/app/users') }}">User</a>
            <a class="btn btn-home-soft" href="{{ url('/app/kios') }}">Toko</a>
        @endif
        <a class="btn btn-home-soft" href="{{ url('/legacy/home') }}">Buka Legacy</a>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-xl-6">
        <div class="card home-chart-card h-100">
            <div class="card-header text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-fill me-2"></i>Menu Terlaris Hari Ini</span>
                <span class="badge text-bg-info">Update: Hari ini</span>
            </div>
            <div class="card-body">
                <div class="chart-wrap">
                    <canvas id="menuTerlarisChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card home-chart-card h-100">
            <div class="card-header text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up-arrow me-2"></i>Menu Terlaris Minggu Ini</span>
                <span class="badge text-bg-info">Update: Minggu ini</span>
            </div>
            <div class="card-body">
                <div class="chart-wrap">
                    <canvas id="menuTerlarisMingguanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-4">
        <div class="card home-feature h-100">
            <div class="card-body text-center">
                <div class="icon text-primary mb-2"><i class="bi bi-clock-history"></i></div>
                <div class="fw-bold mb-1">Pesan Cepat</div>
                <div class="small text-muted">Tidak perlu antri, pesan dalam hitungan detik.</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card home-feature h-100">
            <div class="card-body text-center">
                <div class="icon text-success mb-2"><i class="bi bi-geo-alt"></i></div>
                <div class="fw-bold mb-1">Menu Lengkap</div>
                <div class="small text-muted">Pilihan makanan dan minuman beragam.</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card home-feature h-100">
            <div class="card-body text-center">
                <div class="icon text-danger mb-2"><i class="bi bi-tags"></i></div>
                <div class="fw-bold mb-1">Promo Spesial</div>
                <div class="small text-muted">Dapatkan penawaran terbaik setiap hari.</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($dailyMenuLabels);
        const values = @json($dailyMenuTotals);
        const weeklyLabels = @json($weeklyMenuLabels);
        const weeklyValues = @json($weeklyMenuTotals);
        const colors = ['#ff7a18', '#ffc107', '#4caf50', '#17a2b8', '#dc3545'];

        function renderMenuChart(canvasId, labelsData, valuesData, emptyMessage) {
            if (!labelsData.length) {
                const el = document.getElementById(canvasId);
                if (el) {
                    el.outerHTML = '<div class="text-center py-4 text-muted"><i class="bi bi-graph-up fs-2 d-block mb-2"></i><span>' + emptyMessage + '</span></div>';
                }
                return;
            }

            const ctx = document.getElementById(canvasId);
            if (!ctx) {
                return;
            }

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labelsData,
                    datasets: [{
                        data: valuesData,
                        backgroundColor: labelsData.map((_, i) => colors[i % colors.length]),
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (item) {
                                    return item.raw + ' porsi';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { drawBorder: false }
                        }
                    }
                }
            });
        }

        renderMenuChart('menuTerlarisChart', labels, values, 'Belum ada penjualan hari ini');
        renderMenuChart('menuTerlarisMingguanChart', weeklyLabels, weeklyValues, 'Belum ada penjualan minggu ini');
    });
</script>
@endpush
