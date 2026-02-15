@extends('app.layouts.main')

@section('title', 'Laporan Toko - Sakina Kantin')
@section('page_title', 'Laporan Pendapatan Toko Detail')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan Pendapatan Toko Detail</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow-lg border-0 mb-3">
    <div class="card-header bg-dark text-white fw-bold">
        <i class="bi bi-fork-knife me-2"></i>Laporan Pendapatan Toko Detail
    </div>
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end legacy-form-compact">
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
                <a class="btn btn-outline-primary" href="{{ route('app.reports.toko.export', request()->query()) }}"><i class="bi bi-download me-1"></i>Export CSV</a>
            </div>
        </form>
        <div class="alert alert-info mt-3 mb-0">
            Menampilkan data {{ $kios && $kios !== 'all' ? 'kios '.$kios : 'semua kios' }}{{ $start ? ' dari '.$start : '' }}{{ $end ? ' sampai '.$end : '' }}.
        </div>
    </div>
</div>

<div class="card shadow-lg border-0">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable">
            <caption class="fw-bold">Daftar Pendapatan Toko Detail</caption>
            <thead><tr class="table-head-soft"><th>No</th><th>Kode</th><th>Pelanggan</th><th>Meja</th><th>Pendapatan Toko</th><th>Diskon</th><th>Waktu Order</th><th>Nama Toko</th></tr></thead>
            <tbody>
            @foreach($rows as $idx => $r)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $r->id_order }}</td>
                    <td>{{ $r->pelanggan }}</td>
                    <td>{{ $r->meja }}</td>
                    <td>{{ number_format($r->nominal_toko,0,',','.') }}</td>
                    <td>{{ number_format($r->diskon,0,',','.') }}</td>
                    <td>{{ $r->waktu_order }}</td>
                    <td>{{ $r->nama_kios }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="table table-striped table-bordered w-100 w-lg-50 mt-3">
            <tbody>
            <tr>
                <td class="fw-bold">Total Pendapatan Toko</td>
                <td class="fw-bold text-end">Rp {{ number_format($sumToko,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Total Diskon Toko</td>
                <td class="fw-bold text-end">Rp {{ number_format($sumDiskon,0,',','.') }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
