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
    /* Kartu section halaman order */
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

    .order-sticky-toolbar {
        /* Toolbar dibuat sticky agar filter tetap terlihat */
        position: sticky;
        top: .3rem;
        z-index: 6;
        backdrop-filter: blur(3px);
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
        /* Grid form tambah order agar rapi di desktop dan mobile */
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: .55rem;
    }

    .order-grid-form .field-2 { grid-column: span 2; }
    .order-grid-form .field-3 { grid-column: span 3; }
    .order-grid-form .field-12 { grid-column: span 12; }

    .order-actions .btn {
        border-radius: .4rem;
        min-width: 2.1rem;
        height: 2.05rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .order-paybox {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: .5rem;
        padding: .45rem .5rem;
    }

    .quick-pay-summary {
        margin-top: .35rem;
        border-top: 1px dashed #d6e0f3;
        padding-top: .35rem;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .35rem;
    }

    .quick-pay-summary .item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: .4rem;
        padding: .3rem .4rem;
    }

    .quick-pay-summary .label {
        font-size: .67rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        font-weight: 800;
        margin-bottom: .05rem;
    }

    .quick-pay-summary .value {
        font-size: .78rem;
        font-weight: 800;
        color: #1f2937;
    }

    .quick-pay-error {
        margin-top: .3rem;
        font-size: .72rem;
        color: #b91c1c;
        font-weight: 700;
        display: none;
    }

    .order-status-wrap {
        min-width: 130px;
    }

    .order-status-meta {
        margin-top: .25rem;
        font-size: .72rem;
        color: #64748b;
        font-weight: 700;
    }

    .order-status-progress {
        margin-top: .22rem;
        height: .32rem;
        border-radius: 999px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .order-status-progress > span {
        display: block;
        height: 100%;
        background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%);
    }

    .order-mobile-actions {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1040;
        background: rgba(255, 255, 255, .96);
        border-top: 1px solid #dbe3f3;
        box-shadow: 0 -6px 20px rgba(15, 23, 42, .1);
        padding: .45rem .55rem calc(.45rem + env(safe-area-inset-bottom));
        display: none;
    }

    .order-mobile-actions .btn {
        border-radius: .55rem;
        font-size: .78rem;
        font-weight: 800;
        padding: .38rem .52rem;
    }

    .order-empty {
        padding: 1rem 0 !important;
    }

    .order-empty .icon {
        font-size: 1.6rem;
        color: #94a3b8;
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

    @media (max-width: 768px) {
        body {
            padding-bottom: 74px;
        }

        .order-mobile-actions {
            display: block;
        }

        .order-table-mobile-card thead {
            display: none;
        }

        .order-table-mobile-card tbody tr {
            display: block;
            border: 1px solid #e2e8f0;
            border-radius: .72rem;
            background: #fff;
            padding: .45rem .52rem;
            margin-bottom: .6rem;
            box-shadow: 0 .12rem .42rem rgba(31, 41, 55, .05);
        }

        .order-table-mobile-card tbody td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .65rem;
            border: 0;
            border-bottom: 1px dashed #edf2f7;
            padding: .33rem 0;
            font-size: .82rem;
        }

        .order-table-mobile-card tbody td::before {
            content: attr(data-label);
            min-width: 88px;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #64748b;
            font-weight: 800;
            padding-top: .12rem;
        }

        .order-table-mobile-card tbody td:last-child {
            border-bottom: 0;
            display: block;
            padding-top: .45rem;
        }

        .order-table-mobile-card tbody td:last-child::before {
            display: block;
            margin-bottom: .32rem;
        }

        .order-table-mobile-card .table-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
@endpush

@section('content')
{{-- Toolbar filter transaksi order --}}
<div id="orderFilterCard" class="card order-page-section shadow-lg border-0 mb-3 order-sticky-toolbar">
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

{{-- Form tambah order baru --}}
<div id="orderCreateCard" class="card order-page-section shadow-lg border-0 mb-3">
    <div class="card-header fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Order</div>
    <div class="card-body">
        <form method="post" action="{{ route('app.orders.store') }}" class="order-grid-form legacy-form-compact">
            @csrf
            <div class="field-2"><label class="form-label">Kode Order</label><input class="form-control bg-light" name="id_order" type="number" value="{{ $nextOrderCode }}" readonly required></div>
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

{{-- Tabel transaksi + aksi detail/edit/bayar/hapus --}}
<div class="card order-page-section shadow-lg border-0">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover table-bordered caption-top align-middle js-datatable order-table-mobile-card" id="table_order">
            <caption class="fw-bold">Daftar Transaksi Order</caption>
            <thead>
            <tr class="table-head-soft">
                <th>No</th>
                <th>Kode</th><th>Pelanggan</th><th>Meja</th><th>Kios</th><th>Total</th><th>Kasir</th><th>Status</th><th style="min-width:235px;">Aksi</th>
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
                    <td data-label="No" class="text-center">{{ $idx + 1 }}</td>
                    <td data-label="Kode">{{ $order->id_order }}</td>
                    <td data-label="Pelanggan">{{ $order->pelanggan }}</td>
                    <td data-label="Meja">{{ $order->meja }}</td>
                    <td data-label="Kios">{{ $order->nama_kios }}</td>
                    <td data-label="Total" class="fw-bold text-success">{{ number_format($final, 0, ',', '.') }}</td>
                    <td data-label="Kasir">{{ $order->kasirUser?->username }}</td>
                    <td data-label="Status">
                        <div class="order-status-wrap">
                            @if($isPaid)
                                <span class="app-badge success"><i class="bi bi-check-circle-fill"></i>Dibayar</span>
                                <div class="order-status-meta">Lunas 100%</div>
                                <div class="order-status-progress"><span style="width: 100%"></span></div>
                            @else
                                <span class="app-badge danger"><i class="bi bi-x-circle-fill"></i>Belum Dibayar</span>
                                <div class="order-status-meta">Menunggu pembayaran</div>
                                <div class="order-status-progress"><span style="width: 0%"></span></div>
                            @endif
                        </div>
                    </td>
                    <td data-label="Aksi">
                        <div class="table-actions order-actions">
                            <a class="btn btn-sm btn-info" title="Detail Order" data-bs-toggle="tooltip" data-bs-placement="top" href="{{ url('/app/orders/'.$order->id_order) }}"><i class="bi bi-eye-fill"></i></a>
                            <button class="btn btn-sm btn-warning" type="button" title="Edit Order" data-bs-toggle="modal" data-bs-target="#editOrderModal{{ $order->id_order }}">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            @if($isPaid)
                                <a
                                    class="btn btn-sm btn-secondary"
                                    title="Print Struk"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    href="{{ route('app.orders.receipt', ['id' => $order->id_order, 'autoprint' => 1]) }}"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    <i class="bi bi-printer-fill"></i>
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary" type="button" title="Print Struk (setelah dibayar)" disabled>
                                    <i class="bi bi-printer-fill"></i>
                                </button>
                            @endif
                            @if(!$isPaid)
                                <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-placement="top" data-bs-target="#quickPay{{ $order->id_order }}" aria-expanded="false" aria-controls="quickPay{{ $order->id_order }}" title="Bayar Cepat">
                                    <i class="bi bi-cash-coin"></i>
                                </button>
                            @endif
                            <form method="post" action="{{ route('app.orders.destroy', $order->id_order) }}" onsubmit="return confirm('Hapus order ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Hapus Order" data-bs-toggle="tooltip" data-bs-placement="top" type="submit"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                        @if(!$isPaid)
                            <div class="collapse mt-2" id="quickPay{{ $order->id_order }}">
                                <form method="post" action="{{ route('app.orders.pay', $order->id_order) }}" class="row g-1 order-paybox js-quick-pay-form" data-order-total="{{ (int) $final }}">
                                    @csrf
                                    <div class="col-6">
                                        <label class="form-label mb-1 small fw-bold">Diskon (Rp)</label>
                                        <input class="form-control form-control-sm js-quick-pay-discount" name="diskon" placeholder="0" value="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-1 small fw-bold">Bayar (Rp)</label>
                                        <input class="form-control form-control-sm js-quick-pay-amount" name="bayar" placeholder="Nominal Bayar" value="{{ number_format($final, 0, ',', '.') }}" required>
                                    </div>
                                    <div class="col-12 quick-pay-summary">
                                        <div class="item">
                                            <div class="label">Tagihan</div>
                                            <div class="value js-quick-pay-total">Rp {{ number_format((int) $final, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="item">
                                            <div class="label">Diskon</div>
                                            <div class="value js-quick-pay-discount-show">Rp 0</div>
                                        </div>
                                        <div class="item">
                                            <div class="label">Sisa Bayar</div>
                                            <div class="value js-quick-pay-due">Rp {{ number_format((int) $final, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12 quick-pay-error js-quick-pay-error"></div>
                                    <div class="col-12">
                                        <button class="btn btn-sm btn-primary w-100 js-quick-pay-submit" type="submit"><i class="bi bi-cash-coin me-1"></i>Bayar Cepat</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center order-empty" colspan="9">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <i class="bi bi-inbox icon"></i>
                            <div class="fw-bold">Belum ada order</div>
                            <div class="small text-muted">Buat order baru dari form di atas untuk mulai transaksi.</div>
                            <a href="#orderCreateCard" class="btn btn-sm btn-primary mt-2"><i class="bi bi-plus-circle me-1"></i>Buat Order Sekarang</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="order-mobile-actions d-md-none">
    <div class="d-grid gap-2" style="grid-template-columns: repeat(3, minmax(0,1fr));">
        <a href="#orderCreateCard" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Order</a>
        <a href="#orderFilterCard" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</a>
        <button type="button" class="btn btn-outline-success" id="openFirstQuickPayBtn"><i class="bi bi-cash-coin me-1"></i>Quick Pay</button>
    </div>
</div>

{{-- Modal edit order untuk tiap baris data --}}
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function parseRupiah(value) {
            const raw = String(value || '').replace(/[^0-9]/g, '');
            return raw ? Number(raw) : 0;
        }

        function formatRupiah(value) {
            const amount = Math.max(0, Number(value || 0));
            return amount.toLocaleString('id-ID');
        }

        function updateQuickPayState(form) {
            const total = Number(form.dataset.orderTotal || 0);
            const discountInput = form.querySelector('.js-quick-pay-discount');
            const amountInput = form.querySelector('.js-quick-pay-amount');
            const discountShow = form.querySelector('.js-quick-pay-discount-show');
            const dueShow = form.querySelector('.js-quick-pay-due');
            const errorEl = form.querySelector('.js-quick-pay-error');
            const submitBtn = form.querySelector('.js-quick-pay-submit');
            if (!discountInput || !amountInput || !discountShow || !dueShow || !errorEl || !submitBtn) {
                return;
            }

            const discount = parseRupiah(discountInput.value);
            const amount = parseRupiah(amountInput.value);
            discountInput.value = formatRupiah(discount);
            amountInput.value = formatRupiah(amount);

            const appliedDiscount = Math.min(discount, total);
            const due = Math.max(0, total - appliedDiscount);
            discountShow.textContent = 'Rp ' + formatRupiah(appliedDiscount);
            dueShow.textContent = 'Rp ' + formatRupiah(due);

            let error = '';
            if (discount > total) {
                error = 'Diskon tidak boleh melebihi tagihan.';
            } else if (amount < due) {
                error = 'Nominal bayar kurang dari sisa tagihan.';
            }

            if (error) {
                errorEl.style.display = 'block';
                errorEl.textContent = error;
                submitBtn.disabled = true;
            } else {
                errorEl.style.display = 'none';
                errorEl.textContent = '';
                submitBtn.disabled = false;
            }
        }

        document.querySelectorAll('.js-quick-pay-form').forEach(function (form) {
            const discountInput = form.querySelector('.js-quick-pay-discount');
            const amountInput = form.querySelector('.js-quick-pay-amount');
            if (discountInput) {
                discountInput.addEventListener('input', function () {
                    updateQuickPayState(form);
                });
                discountInput.addEventListener('blur', function () {
                    updateQuickPayState(form);
                });
            }
            if (amountInput) {
                amountInput.addEventListener('input', function () {
                    updateQuickPayState(form);
                });
                amountInput.addEventListener('blur', function () {
                    updateQuickPayState(form);
                });
            }
            form.addEventListener('submit', function (event) {
                updateQuickPayState(form);
                const submitBtn = form.querySelector('.js-quick-pay-submit');
                if (submitBtn && submitBtn.disabled) {
                    event.preventDefault();
                }
            });
            updateQuickPayState(form);
        });

        const openFirstQuickPayBtn = document.getElementById('openFirstQuickPayBtn');
        if (openFirstQuickPayBtn) {
            openFirstQuickPayBtn.addEventListener('click', function () {
                const trigger = document.querySelector('button[data-bs-target^="#quickPay"]');
                if (!trigger) {
                    return;
                }
                const targetSelector = trigger.getAttribute('data-bs-target');
                const target = targetSelector ? document.querySelector(targetSelector) : null;
                if (!target) {
                    return;
                }
                const collapse = window.bootstrap && window.bootstrap.Collapse
                    ? window.bootstrap.Collapse.getOrCreateInstance(target, { toggle: false })
                    : null;
                if (collapse) {
                    collapse.show();
                }
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const payInput = target.querySelector('.js-quick-pay-amount');
                if (payInput) {
                    setTimeout(function () {
                        payInput.focus();
                        payInput.select();
                    }, 260);
                }
            });
        }

        if (window.bootstrap && typeof window.bootstrap.Tooltip === 'function') {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                window.bootstrap.Tooltip.getOrCreateInstance(el);
            });
        }
    });
</script>
@endpush
