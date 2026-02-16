@extends('app.layouts.main')

@section('title', 'Laporan Detail - Sakina Kantin')
@section('page_title', 'Laporan Pendapatan Detail')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan Pendapatan Detail</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    /* Kartu section laporan */
    .report-page-section {
        border: 1px solid #e3e6f0;
        border-radius: .7rem;
        overflow: hidden;
    }

    .report-page-section .card-header {
        background: #111827;
        color: #fff;
        border-bottom: 0;
    }

    .report-filter-toolbar {
        /* Toolbar filter dibuat sticky agar tetap terlihat saat scroll */
        position: sticky;
        top: .3rem;
        z-index: 6;
        backdrop-filter: blur(3px);
    }

    .app-empty-state {
        padding: 1rem 0 !important;
    }

    .app-empty-state .icon {
        font-size: 1.55rem;
        color: #94a3b8;
    }
</style>
@endpush

@section('content')
{{-- Toolbar filter + export laporan detail --}}
<div class="card report-page-section shadow-lg border-0 mb-3 report-filter-toolbar">
    <div class="card-header fw-bold">
        <i class="bi bi-fork-knife me-2"></i>Laporan Pendapatan Detail
    </div>
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end legacy-form-compact js-report-filter" data-loading-target="ordersReportLoading">
            <div class="col-md-3"><label class="form-label">Tanggal Mulai</label><input class="form-control" type="date" name="start_date" value="{{ $start }}"></div>
            <div class="col-md-3"><label class="form-label">Tanggal Akhir</label><input class="form-control" type="date" name="end_date" value="{{ $end }}"></div>
            <div class="col-md-3">
                <label class="form-label">Kios</label>
                <select class="form-select js-select2" name="kios_filter">
                    <option value="">Pilih Kios</option>
                    <option value="all" @selected($kios==='all')>All</option>
                    @foreach($kiosList as $k)
                        <option value="{{ $k->nama }}" @selected($kios===$k->nama)>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a class="btn btn-outline-primary" href="{{ route('app.reports.orders.export', request()->query()) }}"><i class="bi bi-download me-1"></i>Export Excel</a>
                <a class="btn btn-outline-danger" href="{{ route('app.reports.orders.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
            </div>
        </form>
        <div class="alert alert-info mt-3 mb-0">
            Menampilkan data {{ $kios && $kios !== 'all' ? 'kios '.$kios : 'semua kios' }}{{ $start ? ' dari '.$start : '' }}{{ $end ? ' sampai '.$end : '' }}.
        </div>
    </div>
</div>

{{-- Tabel laporan + rekap total --}}
<div class="card report-page-section shadow-lg border-0 position-relative">
    <div class="report-loading-overlay" id="ordersReportLoading" aria-hidden="true">
        <div class="d-flex align-items-center gap-2 fw-bold text-primary">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Memuat laporan...
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable legacy-report-table">
            <caption class="fw-bold">Daftar Pendapatan Detail</caption>
            <thead>
            <tr class="table-head-soft">
                <th>No</th><th>Kode</th><th>Pelanggan</th><th>Meja</th><th>Pendapatan Toko</th><th>Pendapatan Sakina Food Court</th><th>Total Bayar</th><th>Status</th><th>Diskon</th><th>Waktu Order</th><th>Nama Toko</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $idx => $r)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $r->id_order }}</td>
                    <td>{{ $r->pelanggan }}</td>
                    <td>{{ $r->meja }}</td>
                    <td>{{ number_format($r->nominal_toko,0,',','.') }}</td>
                    <td>{{ number_format($r->nominal_rs,0,',','.') }}</td>
                    <td>{{ number_format($r->jumlah_bayar,0,',','.') }}</td>
                    <td>
                        @if(!empty($r->id_bayar))
                            <span class="app-badge success"><i class="bi bi-check-circle-fill"></i>Dibayar</span>
                        @else
                            <span class="app-badge danger"><i class="bi bi-x-circle-fill"></i>Belum Dibayar</span>
                        @endif
                    </td>
                    <td>{{ number_format($r->diskon,0,',','.') }}</td>
                    <td>{{ $r->waktu_order }}</td>
                    <td>{{ $r->nama_kios }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center app-empty-state" colspan="11">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <i class="bi bi-inbox icon"></i>
                            <div class="fw-bold">Tidak ada data untuk filter ini</div>
                            <div class="small text-muted">Ubah rentang tanggal atau kios lalu coba lagi.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <table class="table table-striped table-bordered w-100 w-lg-50 mt-3">
            <thead>
            <tr class="table-head-soft">
                <th colspan="2" class="text-center">REKAPITULASI TOTAL</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="fw-bold">Total Pendapatan Toko</td>
                <td class="fw-bold text-end">Rp {{ number_format($sumToko,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Total Pendapatan Sakina Food Court</td>
                <td class="fw-bold text-end">Rp {{ number_format($sumRs,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Total Diskon</td>
                <td class="fw-bold text-end">Rp {{ number_format($sumDiskon,0,',','.') }}</td>
            </tr>
            <tr class="table-success">
                <td class="fw-bolder">GRAND TOTAL KOTOR (Toko + Food Court + Diskon)</td>
                <td class="fw-bolder text-end">Rp {{ number_format($sumToko + $sumRs + $sumDiskon,0,',','.') }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

