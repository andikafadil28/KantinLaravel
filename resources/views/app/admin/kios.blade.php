@extends('app.layouts.main')

@section('title', 'Manajemen Toko - Sakina Kantin')
@section('page_title', 'Manajemen Toko')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manajemen Toko</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    /* Kartu section modul admin */
    .admin-page-section {
        border: 1px solid #e3e6f0;
        border-radius: .7rem;
        overflow: hidden;
    }

    .admin-page-section .card-header {
        background: #111827;
        color: #fff;
        border-bottom: 0;
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
{{-- Form tambah toko/kios --}}
<div class="card admin-page-section shadow-lg border-0 mb-3">
    <div class="card-header fw-bold">
        <i class="bi bi-building me-2"></i>Manajemen Toko
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('app.kios.store') }}" class="row g-2 legacy-form-compact">
            @csrf
            <div class="col-md-6"><input class="form-control" name="nama" placeholder="Nama toko" required></div>
            <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

{{-- Tabel kios + aksi edit/hapus --}}
<div class="card admin-page-section shadow-lg border-0">
    <div class="card-header fw-bold"><i class="bi bi-shop-window me-2"></i>Daftar Toko</div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable">
            <caption class="fw-bold">Daftar Toko</caption>
            <thead><tr class="table-head-soft"><th>ID</th><th>Nama</th><th style="min-width:300px;">Aksi</th></tr></thead>
            <tbody>
            @forelse($kios as $k)
                @php($isActive = (int)($k->status ?? 1) === 1)
                <tr>
                    <td>{{ $k->id }}</td>
                    <td>
                        {{ $k->nama }}
                        <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }} ms-1">{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editKiosModal{{ $k->id }}" aria-label="Edit toko {{ $k->nama }}" title="Edit toko">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <form method="post" action="{{ route('app.kios.status', $k->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $isActive ? 0 : 1 }}">
                                <button class="btn btn-sm {{ $isActive ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit" aria-label="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }} toko {{ $k->nama }}" title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }} toko">
                                    <i class="bi {{ $isActive ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                </button>
                            </form>
                            <form method="post" action="{{ route('app.kios.destroy', $k->id) }}" onsubmit="return confirm('Hapus kios ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" aria-label="Hapus toko {{ $k->nama }}" title="Hapus toko"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center app-empty-state" colspan="3">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <i class="bi bi-inbox icon"></i>
                            <div class="fw-bold">Belum ada data toko</div>
                            <div class="small text-muted">Tambahkan toko baru dari form di atas.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal edit kios per baris data --}}
@foreach($kios as $k)
    <div class="modal fade app-modal" id="editKiosModal{{ $k->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('app.kios.update', $k->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-shop me-2"></i>Edit Toko: {{ $k->nama }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Nama Toko</label>
                        <input class="form-control" name="nama" value="{{ $k->nama }}" required>
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
