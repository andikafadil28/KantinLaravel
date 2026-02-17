@extends('app.layouts.main')

@section('title', 'Log Transaksi - Sakina Kantin')
@section('page_title', 'Log Transaksi')
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Log Transaksi</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header fw-bold">
        <i class="bi bi-journal-text me-2"></i>Audit Trail Transaksi
    </div>
    <div class="card-body table-responsive">
        <div class="alert alert-info">
            Menampilkan maksimal 1000 log terbaru. Data ini khusus admin.
        </div>
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable">
            <caption class="fw-bold">Riwayat Aktivitas Transaksi</caption>
            <thead class="table-head-soft">
                <tr>
                    <th style="width:70px;">ID</th>
                    <th style="width:150px;">Waktu</th>
                    <th style="width:120px;">Aksi</th>
                    <th style="width:110px;">Order</th>
                    <th style="width:180px;">Admin</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->created_at }}</td>
                        <td><span class="app-badge secondary">{{ $log->action }}</span></td>
                        <td>{{ $log->order_id ?? '-' }}</td>
                        <td>
                            {{ $log->actor_username ?? '-' }}
                            @if(!empty($log->actor_id))
                                <small class="text-muted">(ID: {{ $log->actor_id }})</small>
                            @endif
                        </td>
                        <td>{{ $log->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada log transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
