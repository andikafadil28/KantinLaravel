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

@section('content')
<div class="card shadow-lg border-0 mb-3">
    <div class="card-header bg-dark text-white fw-bold">
        <i class="bi bi-building me-2"></i>Manajemen Toko
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('app.kios.store') }}" class="row g-2">
            @csrf
            <div class="col-md-6"><input class="form-control" name="nama" placeholder="Nama toko" required></div>
            <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

<div class="card shadow-lg border-0">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable">
            <caption class="fw-bold">Daftar Toko</caption>
            <thead><tr class="table-head-soft"><th>ID</th><th>Nama</th><th style="min-width:300px;">Aksi</th></tr></thead>
            <tbody>
            @foreach($kios as $k)
                <tr>
                    <td>{{ $k->id }}</td>
                    <td>{{ $k->nama }}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editKiosModal{{ $k->id }}">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <form method="post" action="{{ route('app.kios.destroy', $k->id) }}" onsubmit="return confirm('Hapus kios ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

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
