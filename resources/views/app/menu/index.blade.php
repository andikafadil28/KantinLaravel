@extends('app.layouts.main')

@section('title', 'Manajemen Menu - Sakina Kantin')
@section('page_title', 'Manajemen Menu')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manajemen Menu</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    /* Kartu section halaman menu */
    .menu-page-section {
        border: 1px solid #e3e6f0;
        border-radius: .7rem;
        overflow: hidden;
    }

    .menu-page-section .card-header {
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
{{-- Form tambah menu baru --}}
<div class="card menu-page-section shadow-lg border-0 mb-3">
    <div class="card-header fw-bold">
        <i class="bi bi-fork-knife me-2"></i>Menu
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('app.menu.store') }}" enctype="multipart/form-data" class="row g-3 legacy-form-compact">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Nama</label>
                <input class="form-control" type="text" name="nama" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori</label>
                <select class="form-select js-select2" name="kategori" required>
                    @foreach($kategories as $item)
                        <option value="{{ $item->id_kategori }}">{{ $item->kategori_menu }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Kios</label>
                <select class="form-select js-select2" name="nama_toko" required>
                    @foreach($kios as $item)
                        <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Harga</label>
                <input class="form-control" type="number" step="0.01" name="harga" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Pajak</label>
                <input class="form-control" type="number" step="0.01" name="pajak" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Foto (opsional)</label>
                <input class="form-control" type="file" name="foto" accept="image/*">
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <input class="form-control" type="text" name="keterangan" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Tabel daftar menu + aksi edit/hapus --}}
<div class="card menu-page-section shadow-lg border-0">
    <div class="card-header fw-bold"><i class="bi bi-list-ul me-2"></i>Daftar Menu</div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable">
            <caption class="fw-bold">Daftar Menu</caption>
            <thead>
            <tr class="table-head-soft">
                <th>ID</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Kios</th>
                <th>Harga</th>
                <th>Pajak</th>
                <th style="min-width:300px;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($menus as $menu)
                <tr>
                    <td>{{ $menu->id }}</td>
                    <td>
                        @if($menu->foto)
                            <img src="{{ str_starts_with($menu->foto, 'menu/') ? asset('storage/'.$menu->foto) : url('/legacy/assets/img/'.$menu->foto) }}" alt="{{ $menu->nama }}" width="64" class="rounded">
                        @endif
                    </td>
                    <td>{{ $menu->nama }}<br><small class="text-muted">{{ $menu->keterangan }}</small></td>
                    <td>{{ $menu->nama_toko }}</td>
                    <td>{{ number_format((float)$menu->harga, 0, ',', '.') }}</td>
                    <td>{{ number_format((float)$menu->pajak, 0, ',', '.') }}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editMenuModal{{ $menu->id }}" aria-label="Edit menu {{ $menu->nama }}" title="Edit menu">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                                <form method="post" action="{{ route('app.menu.destroy', $menu->id) }}" onsubmit="return confirm('Hapus menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit" aria-label="Hapus menu {{ $menu->nama }}" title="Hapus menu"><i class="bi bi-trash-fill"></i></button>
                                </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center app-empty-state" colspan="7">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <i class="bi bi-inbox icon"></i>
                            <div class="fw-bold">Belum ada data menu</div>
                            <div class="small text-muted">Tambahkan menu baru dari form di atas.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal edit per baris menu --}}
@foreach($menus as $menu)
    <div class="modal fade app-modal" id="editMenuModal{{ $menu->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('app.menu.update', $menu->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Menu: {{ $menu->nama }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input class="form-control" name="nama" value="{{ $menu->nama }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input class="form-control" name="keterangan" value="{{ $menu->keterangan }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Harga</label>
                                <input class="form-control" type="number" step="0.01" name="harga" value="{{ $menu->harga }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pajak</label>
                                <input class="form-control" type="number" step="0.01" name="pajak" value="{{ $menu->pajak }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ganti Foto (opsional)</label>
                                <input class="form-control" type="file" name="foto" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="kategori" required>
                                    @foreach($kategories as $item)
                                        <option value="{{ $item->id_kategori }}" @selected((int)$menu->kategori === (int)$item->id_kategori)>{{ $item->kategori_menu }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kios</label>
                                <select class="form-select" name="nama_toko" required>
                                    @foreach($kios as $item)
                                        <option value="{{ $item->nama }}" @selected($menu->nama_toko === $item->nama)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
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
