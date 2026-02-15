@extends('app.layouts.main')

@section('title', 'Detail Order - Sakina Kantin')
@section('page_title', 'Detail Order ' . $order->id_order)
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/app/orders') }}">Transaksi Order</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail {{ $order->id_order }}</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .order-meta {
        border: 1px solid #e3e6f0;
        border-radius: .7rem;
        background: #fff;
    }

    .order-meta .meta-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem;
    }

    .order-meta .meta-item {
        background: #f8f9fc;
        border: 1px solid #e5e9f2;
        border-radius: .6rem;
        padding: .55rem .65rem;
    }

    .order-meta .meta-label {
        font-size: .72rem;
        color: #6b7280;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .1rem;
    }

    .order-meta .meta-value {
        font-size: .94rem;
        color: #1f2937;
        font-weight: 800;
    }

    .order-section {
        border: 1px solid #e3e6f0;
        border-radius: .7rem;
        overflow: hidden;
    }

    .order-section .card-header {
        background: #111827;
        color: #fff;
        border-bottom: 0;
    }

    .order-items-table thead th {
        background: #f1f5f9;
    }

    .order-items-table td,
    .order-items-table th {
        white-space: nowrap;
    }

    @media (max-width: 992px) {
        .order-meta .meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

@section('content')
<div class="card order-meta mb-3">
    <div class="card-body">
        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-label">Kode Order</div>
                <div class="meta-value">{{ $order->id_order }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Pelanggan</div>
                <div class="meta-value">{{ $order->pelanggan }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Meja</div>
                <div class="meta-value">{{ $order->meja }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Kios</div>
                <div class="meta-value">{{ $order->nama_kios }}</div>
            </div>
        </div>
        <div class="mt-2">
            <span class="app-badge {{ $isPaid ? 'success' : 'danger' }}">
                <i class="bi {{ $isPaid ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                {{ $isPaid ? 'Dibayar' : 'Belum Dibayar' }}
            </span>
        </div>
    </div>
</div>

<div class="card order-section mb-3">
    <div class="card-header fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Item</div>
    <div class="card-body">
        <form method="post" action="{{ route('app.orders.items.store', $order->id_order) }}" class="row g-2 legacy-form-compact">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Menu</label>
                <select class="form-select js-select2" name="menu" required>
                    <option value="">Pilih</option>
                    @foreach($menus as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Jumlah</label><input class="form-control" type="number" name="jumlah" min="1" required></div>
            <div class="col-md-5"><label class="form-label">Catatan</label><input class="form-control" name="catatan_order"></div>
            <div class="col-12"><button class="btn {{ $canModify ? 'btn-primary' : 'btn-secondary' }}" type="submit" {{ $canModify ? '' : 'disabled' }}><i class="bi bi-plus-circle me-1"></i>Tambah</button></div>
        </form>
    </div>
</div>

<div class="card order-section mb-3">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle js-datatable order-items-table">
            <thead><tr><th>Menu</th><th>Harga</th><th>Qty</th><th>Catatan</th><th>Total</th><th style="min-width:260px;">Aksi</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                @php $item = $row['item']; @endphp
                <tr>
                    <td>{{ $row['menu']?->nama }}</td>
                    <td>{{ number_format($row['harga_jual'], 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->catatan_order }}</td>
                    <td>{{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                    <td>
                        <form method="post" action="{{ route('app.orders.items.update', [$order->id_order, $item->id_list_order]) }}" class="row g-1 mb-2">
                            @csrf
                            <div class="col-12">
                                <select class="form-select form-select-sm js-select2" name="menu" required>
                                    @foreach($menus as $m)<option value="{{ $m->id }}" @selected((int)$item->menu === (int)$m->id)>{{ $m->nama }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-4"><input class="form-control form-control-sm" type="number" name="jumlah" min="1" value="{{ $item->jumlah }}" required></div>
                            <div class="col-8"><input class="form-control form-control-sm" name="catatan_order" value="{{ $item->catatan_order }}"></div>
                            <div class="col-12"><button class="btn btn-sm {{ $canModify ? 'btn-warning' : 'btn-secondary' }}" type="submit" {{ $canModify ? '' : 'disabled' }}><i class="bi bi-pencil-square me-1"></i>Simpan</button></div>
                        </form>
                        <form method="post" action="{{ route('app.orders.items.destroy', [$order->id_order, $item->id_list_order]) }}" onsubmit="return confirm('Hapus item ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm {{ $canModify ? 'btn-danger' : 'btn-secondary' }}" type="submit" {{ $canModify ? '' : 'disabled' }}><i class="bi bi-trash me-1"></i>Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">Belum ada item.</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <table class="table table-striped table-bordered w-100 w-lg-50 mt-3 mb-0">
            <tbody>
            <tr>
                <td class="fw-bold">Total</td>
                <td class="fw-bold text-end">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Diskon</td>
                <td class="fw-bold text-end">{{ number_format($diskonExisting, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-success">
                <td class="fw-bold">Grand Total</td>
                <td class="fw-bold text-end">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card order-section">
    <div class="card-header fw-bold"><i class="bi bi-cash-coin me-2"></i>Pembayaran</div>
    <div class="card-body">
        <form method="post" action="{{ route('app.orders.pay', $order->id_order) }}" class="row g-2 legacy-form-compact">
            @csrf
            <div class="col-md-4"><label class="form-label">Diskon (Rp)</label><input class="form-control" name="diskon" type="text" inputmode="decimal" value="{{ old('diskon', number_format($diskonExisting, 0, ',', '.')) }}" {{ $isPaid ? 'readonly' : '' }}></div>
            <div class="col-md-4"><label class="form-label">Bayar (Rp)</label><input class="form-control" name="bayar" type="text" inputmode="decimal" value="{{ old('bayar', number_format($grandTotal, 0, ',', '.')) }}" {{ $isPaid ? 'readonly' : '' }}></div>
            <div class="col-md-4"><label class="form-label">PPN</label><input class="form-control" value="{{ number_format($totalPpn, 0, ',', '.') }}" readonly></div>
            <div class="col-12 d-flex flex-wrap gap-2 align-items-end">
                <button class="btn {{ $isPaid ? 'btn-secondary' : 'btn-primary' }}" type="submit" {{ $isPaid ? 'disabled' : '' }}>
                    <i class="bi bi-cash-coin me-1"></i>Proses Bayar
                </button>
                <button
                    type="button"
                    class="btn {{ $isPaid ? 'btn-info' : 'btn-secondary' }}"
                    data-receipt-url="{{ route('app.orders.receipt', ['id' => $order->id_order]) }}"
                    onclick="printReceipt(this.dataset.receiptUrl)"
                    {{ $isPaid ? '' : 'disabled' }}
                >
                    <i class="bi bi-printer me-1"></i>Print Struk
                </button>
                @if($isPaid)
                    <div class="d-flex align-items-center gap-2 ms-sm-2">
                        <label for="printModeSelect" class="small text-muted mb-0">Mode Print</label>
                        <select id="printModeSelect" class="form-select form-select-sm" style="width: 120px;">
                            <option value="inline">Inline</option>
                            <option value="popup">Popup</option>
                        </select>
                    </div>
                @else
                    <span class="small text-muted align-self-center">Print aktif setelah pembayaran berhasil.</span>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const PRINT_MODE_KEY = 'kantin.print.mode';

    function getPrintMode() {
        const mode = localStorage.getItem(PRINT_MODE_KEY);
        return mode === 'popup' ? 'popup' : 'inline';
    }

    function setPrintMode(mode) {
        localStorage.setItem(PRINT_MODE_KEY, mode === 'popup' ? 'popup' : 'inline');
    }

    function printReceipt(url) {
        if (getPrintMode() === 'popup') {
            openPrintPopup(url);
            return;
        }
        printReceiptInline(url);
    }

    function openPrintPopup(url) {
        const popup = window.open(url + (url.includes('?') ? '&' : '?') + 'autoprint=1', 'print_struk', 'width=420,height=700,menubar=no,toolbar=no,location=no,status=no');
        if (popup) {
            popup.focus();
        }
    }

    function printReceiptInline(url) {
        if (!url) return;

        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.src = url;

        iframe.onload = function () {
            try {
                if (!iframe.contentWindow) {
                    openPrintPopup(url);
                    return;
                }
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (_e) {
                openPrintPopup(url);
            }

            setTimeout(function () {
                if (iframe.parentNode) {
                    iframe.parentNode.removeChild(iframe);
                }
            }, 1500);
        };

        iframe.onerror = function () {
            openPrintPopup(url);
            if (iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }
        };

        document.body.appendChild(iframe);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('printModeSelect');
        if (!select) return;
        select.value = getPrintMode();
        select.addEventListener('change', function () {
            setPrintMode(select.value);
        });
    });
</script>
@endpush
