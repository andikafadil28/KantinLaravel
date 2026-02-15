@extends('app.layouts.main')

@section('title', 'Manajemen User - Sakina Kantin')
@section('page_title', 'Manajemen User')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manajemen User</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
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
<div class="card admin-page-section shadow-lg border-0 mb-3">
    <div class="card-header fw-bold">
        <i class="bi bi-people-fill me-2"></i>Manajemen User
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('app.users.store') }}" class="row g-3 legacy-form-compact">
            @csrf
            <div class="col-md-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
            <div class="col-md-3"><label class="form-label">Password</label><input class="form-control" name="password" required></div>
            <div class="col-md-2"><label class="form-label">Level</label><select class="form-select js-select2" name="level"><option value="1">Admin</option><option value="3">Kasir</option></select></div>
            <div class="col-md-4"><label class="form-label">Kios</label><select class="form-select js-select2" name="Kios">@foreach($kios as $k)<option value="{{ $k->nama }}">{{ $k->nama }}</option>@endforeach</select></div>
            <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

<div class="card admin-page-section shadow-lg border-0">
    <div class="card-header fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Daftar User</div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable">
            <caption class="fw-bold">Daftar User</caption>
            <thead><tr class="table-head-soft"><th>ID</th><th>Username</th><th>Level</th><th>Kios</th><th style="min-width:280px;">Aksi</th></tr></thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->username }}</td>
                    <td>
                        @if((int) $u->level === 1)
                            <span class="app-badge warning"><i class="bi bi-shield-lock-fill"></i>Admin</span>
                        @else
                            <span class="app-badge info"><i class="bi bi-person-badge-fill"></i>Kasir</span>
                        @endif
                    </td>
                    <td>{{ $u->Kios }}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" aria-label="Edit user {{ $u->username }}" title="Edit user">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <form method="post" action="{{ route('app.users.destroy', $u->id) }}" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" aria-label="Hapus user {{ $u->username }}" title="Hapus user"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center app-empty-state" colspan="5">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <i class="bi bi-inbox icon"></i>
                            <div class="fw-bold">Belum ada data user</div>
                            <div class="small text-muted">Tambahkan user baru dari form di atas.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($users as $u)
    <div class="modal fade app-modal" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('app.users.update', $u->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>Edit User: {{ $u->username }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Username</label>
                            <input class="form-control" name="username" value="{{ $u->username }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Password Baru (opsional)</label>
                            <input class="form-control" name="password" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Level</label>
                            <select class="form-select" name="level">
                                <option value="1" @selected((int)$u->level===1)>Admin</option>
                                <option value="3" @selected((int)$u->level===3)>Kasir</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Kios</label>
                            <select class="form-select" name="Kios">
                                @foreach($kios as $k)
                                    <option value="{{ $k->nama }}" @selected($u->Kios===$k->nama)>{{ $k->nama }}</option>
                                @endforeach
                            </select>
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
