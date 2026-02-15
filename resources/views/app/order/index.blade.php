@extends('app.layouts.main')

@section('title', 'Transaksi Order - Sakina Kantin')
@section('page_title', 'Transaksi Order')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Transaksi Order</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .order-page-section {
        border: 1px solid #e3e6f0;
        border-radius: .7rem;
        overflow: hidden;
    }

    .order-page-section .card-header {
        background: #111827;
        color: #fff;
        border-bottom: 0;
    }

    .order-info-note {
        background: #eef2ff;
        border: 1px solid #dbe4ff;
        color: #374151;
        border-radius: .6rem;
        padding: .55rem .7rem;
        font-size: .82rem;
    }

    .order-grid-form {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: .55rem;
    }

    .order-grid-form .field-2 { grid-column: span 2; }
    .order-grid-form .field-3 { grid-column: span 3; }
    .order-grid-form .field-12 { grid-column: span 12; }

    .order-actions .btn {
        border-radius: .4rem;
    }

    .order-paybox {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: .5rem;
        padding: .45rem;
    }

    @media (max-width: 992px) {
        .order-grid-form .field-2,
        .order-grid-form .field-3 {
            grid-column: span 6;
        }
    }

    @media (max-width: 680px) {
        .order-grid-form .field-2,
        .order-grid-form .field-3 {
            grid-column: span 12;
        }
    }
</style>
@endpush

@section('content')
<div class="card order-page-section shadow-lg border-0 mb-3">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-gear-fill me-2"></i>Manajemen Transaksi Order
    </div>
    <div class="card-body">
        <div class="order-info-note mb-2">
            Pembayaran bisa dari tombol <strong>Detail</strong> atau langsung lewat form <strong>Bayar Cepat</strong> di kolom aksi.
        </div>
        <form method="get" action="{{ url('/app/orders') }}" class="row g-2 align-items-end legacy-form-compact">
            <div class="col-md-4">
                <label class="form-label">Filter Kios</label>
                <select class="form-select js-select2" name="kios_filter">
                    <option value="">Pilih</option>
                    <option value="all" @selected($selectedKios === 'all')>All</option>
                    @foreach($kios as $k)
                        <option value="{{ $k->nama }}" @selected($selectedKios === $k->nama)>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button></div>
        </form>
    </div>
</div>

<div class="card order-page-section shadow-lg border-0 mb-3">
    <div class="card-header fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Order</div>
    <div class="card-body">
        <form method="post" action="{{ route('app.orders.store') }}" class="order-grid-form legacy-form-compact">
            @csrf
            <div class="field-2"><label class="form-label">Kode Order</label><input class="form-control" name="id_order" type="number" value="{{ $nextOrderCode }}" required></div>
            <div class="field-2"><label class="form-label">Meja</label><input class="form-control" name="meja" required></div>
            <div class="field-3"><label class="form-label">Pelanggan</label><input class="form-control" name="pelanggan" required></div>
            <div class="field-3">
                <label class="form-label">Kios</label>
                <select class="form-select js-select2" name="nama_kios" required>
                    @foreach($kios as $k)<option value="{{ $k->nama }}">{{ $k->nama }}</option>@endforeach
                </select>
            </div>
            <div class="field-2"><label class="form-label">Catatan</label><input class="form-control" name="catatan"></div>
            <div class="field-12"><button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1"></i>Buat Order</button></div>
        </form>
    </div>
</div>

<div class="card order-page-section shadow-lg border-0">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable" id="table_order">
            <caption class="fw-bold">Daftar Transaksi Order</caption>
            <thead>
            <tr class="table-head-soft">
                <th>No</th>
                <th>Kode</th><th>Pelanggan</th><th>Meja</th><th>Kios</th><th>Total</th><th>Kasir</th><th>Status</th><th style="min-width:280px;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $idx => $order)
                @php
                    $sum = 0;
                    foreach ($order->items as $item) {
                        $harga = (float)($item->menuRel?->harga ?? 0);
                        $pajak = (float)($item->menuRel?->pajak ?? 0);
                        $sum += (($harga + $pajak) * 1.11) * (int)$item->jumlah;
                    }
                    $diskon = (float)($order->pembayaran?->diskon ?? 0);
                    $final = max(0, ceil($sum - $diskon));
                    $isPaid = $order->pembayaran !== null;
                @endphp
            <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $order->id_order }}</td>
                    <td>{{ $order->pelanggan }}</td>
                    <td>{{ $order->meja }}</td>
                    <td>{{ $order->nama_kios }}</td>
                    <td class="fw-bold text-success">{{ number_format($final, 0, ',', '.') }}</td>
                    <td>{{ $order->kasirUser?->username }}</td>
                    <td>
                        @if($isPaid)
                            <span class="app-badge success"><i class="bi bi-check-circle-fill"></i>Dibayar</span>
                        @else
                            <span class="app-badge danger"><i class="bi bi-x-circle-fill"></i>Belum Dibayar</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions mb-2 order-actions">
                            <a class="btn btn-sm btn-info" title="Detail" href="{{ url('/app/orders/'.$order->id_order) }}"><i class="bi bi-eye-fill"></i></a>
                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editOrderModal{{ $order->id_order }}">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                        @if(!$isPaid)
                            <form method="post" action="{{ route('app.orders.pay', $order->id_order) }}" class="row g-1 mb-2 order-paybox">
                                @csrf
                                <div class="col-6">
                                    <input class="form-control form-control-sm" name="diskon" placeholder="Diskon" value="0">
                                </div>
                                <div class="col-6">
                                    <input class="form-control form-control-sm" name="bayar" placeholder="Nominal Bayar" value="{{ number_format($final, 0, ',', '.') }}" required>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-sm btn-primary w-100" type="submit"><i class="bi bi-cash-coin me-1"></i>Bayar Cepat</button>
                                </div>
                            </form>
                        @endif
                        <div class="table-actions">
                            <form method="post" action="{{ route('app.orders.destroy', $order->id_order) }}" onsubmit="return confirm('Hapus order ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Hapus" type="submit"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">Belum ada order.</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($orders as $order)
    <div class="modal fade app-modal" id="editOrderModal{{ $order->id_order }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('app.orders.update', $order->id_order) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Edit Order: {{ $order->id_order }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Meja</label>
                                <input class="form-control" name="meja" value="{{ $order->meja }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pelanggan</label>
                                <input class="form-control" name="pelanggan" value="{{ $order->pelanggan }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Kios</label>
                                <select class="form-select" name="nama_kios" required>
                                    @foreach($kios as $k)
                                        <option value="{{ $k->nama }}" @selected($order->nama_kios === $k->nama)>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <input class="form-control" name="catatan" value="{{ $order->catatan }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
