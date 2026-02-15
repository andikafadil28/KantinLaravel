@extends('app.layouts.main')

@section('title', 'Rekap RS - Sakina Kantin')
@section('page_title', 'Laporan Rekap RS')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rekap RS</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow-lg border-0 mb-3">
    <div class="card-header bg-dark text-white fw-bold">
        <i class="bi bi-receipt-cutoff me-2"></i>Rekap RS
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
                <a class="btn btn-outline-primary" href="{{ route('app.reports.rekap_rs.export', request()->query()) }}"><i class="bi bi-download me-1"></i>Export CSV</a>
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
            <caption class="fw-bold">Daftar Rekap RS</caption>
            <thead>
            <tr class="table-head-soft">
                <th>No</th><th>Nama Menu</th><th>Nama Toko</th><th>Total Item Terjual</th><th>Harga Satuan</th><th>Total Harga</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rows as $idx => $r)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $r->nama }}</td>
                    <td>{{ $r->nama_toko }}</td>
                    <td>{{ number_format((float) $r->total_terjual,0,',','.') }}</td>
                    <td>{{ number_format((float) $r->harga_satuan,0,',','.') }}</td>
                    <td>{{ number_format((float) $r->total_harga,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <span class="app-badge info"><i class="bi bi-cash-stack"></i>Grand Total: {{ number_format($sumTotal,0,',','.') }}</span>
    </div>
</div>
@endsection
