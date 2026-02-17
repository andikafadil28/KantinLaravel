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
    /* Ringkasan informasi utama order */
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

    /* Tabel item order */
    .order-items-table thead th {
        background: #f1f5f9;
    }

    .order-items-table td,
    .order-items-table th {
        white-space: nowrap;
    }

    .quick-menu-panel {
        border: 1px dashed #dbe4ff;
        background: #f8faff;
        border-radius: .6rem;
        padding: .65rem;
    }

    .quick-menu-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .4rem;
    }

    .quick-menu-title {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 800;
        color: #3b4a75;
    }

    .quick-menu-list {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .quick-menu-btn {
        border: 1px solid #c8d6ff;
        background: #fff;
        color: #2f4aa2;
        border-radius: 999px;
        padding: .24rem .62rem;
        font-size: .77rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .quick-menu-btn:hover {
        background: #e9efff;
        border-color: #9fb5f5;
    }

    .shortcut-note {
        font-size: .75rem;
        color: #64748b;
    }

    .inline-field-msg {
        margin-top: .25rem;
        font-size: .76rem;
        font-weight: 700;
    }

    .inline-field-msg.error {
        color: #b91c1c;
    }

    .inline-field-msg.ok {
        color: #065f46;
    }

    @media (max-width: 992px) {
        .order-meta .meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

@section('content')
{{-- Panel meta order + status pembayaran --}}
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

{{-- Form tambah item ke order aktif --}}
<div class="card order-section mb-3">
    <div class="card-header fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Item</div>
    <div class="card-body">
        <form id="orderAddItemForm" method="post" action="{{ route('app.orders.items.store', $order->id_order) }}" class="row g-2 legacy-form-compact">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Menu</label>
                <select id="addMenuSelect" class="form-select js-select2" name="menu" required>
                    <option value="">Pilih</option>
                    @foreach($menus as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Jumlah</label><input id="addQtyInput" class="form-control" type="number" name="jumlah" min="1" required></div>
            <div class="col-md-5"><label class="form-label">Catatan</label><input id="addNoteInput" class="form-control" name="catatan_order"></div>
            <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-outline-secondary" href="{{ url('/app/orders') }}"><i class="bi bi-arrow-left me-1"></i>Back ke Transaksi Order</a>
                <button id="addItemSubmitBtn" class="btn {{ $canModify ? 'btn-primary' : 'btn-secondary' }}" type="submit" {{ $canModify ? '' : 'disabled' }}><i class="bi bi-plus-circle me-1"></i>Tambah</button>
                <span class="shortcut-note">Shortcut: <strong>/</strong> fokus menu, <strong>Enter</strong> tambah item, <strong>Ctrl+S</strong> bayar.</span>
            </div>
        </form>
        <div class="quick-menu-panel mt-3">
            <div class="quick-menu-head">
                <div class="quick-menu-title">Recent Menu</div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearQuickHistoryBtn">Reset</button>
            </div>
            <div class="quick-menu-list" id="recentMenuList">
                <span class="text-muted small">Belum ada riwayat pilihan menu.</span>
            </div>
            <div class="quick-menu-head mt-3">
                <div class="quick-menu-title">Favorite Menu</div>
            </div>
            <div class="quick-menu-list" id="favoriteMenuList">
                <span class="text-muted small">Menu favorit akan muncul setelah beberapa transaksi.</span>
            </div>
        </div>
    </div>
</div>

{{-- Daftar item order + edit/hapus item --}}
<div class="card order-section mb-3">
    <div class="card-body table-responsive">
        @if($rows->isNotEmpty() && !$isPaid)
            <div class="d-flex justify-content-end mb-2">
                <form method="post" action="{{ route('app.orders.items.clear', $order->id_order) }}" onsubmit="return confirm('Hapus semua item pada order ini?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">
                        <i class="bi bi-trash3-fill me-1"></i>Hapus Semua Item
                    </button>
                </form>
            </div>
        @endif
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

{{-- Form pembayaran dan aksi cetak struk --}}
<div class="card order-section">
    <div class="card-header fw-bold"><i class="bi bi-cash-coin me-2"></i>Pembayaran</div>
    <div class="card-body">
        <form id="orderPayForm" method="post" action="{{ route('app.orders.pay', $order->id_order) }}" class="row g-2 legacy-form-compact">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Diskon (Rp)</label>
                <input id="discountInput" class="form-control" name="diskon" type="text" inputmode="decimal" value="{{ old('diskon', number_format($diskonExisting, 0, ',', '.')) }}" {{ $isPaid ? 'readonly' : '' }}>
                <div id="discountError" class="inline-field-msg error d-none"></div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bayar (Rp)</label>
                <input id="payAmountInput" class="form-control" name="bayar" type="text" inputmode="decimal" value="{{ old('bayar', number_format($grandTotal, 0, ',', '.')) }}" {{ $isPaid ? 'readonly' : '' }}>
                <div id="payError" class="inline-field-msg error d-none"></div>
            </div>
            <div class="col-md-4"><label class="form-label">PPN</label><input class="form-control" value="{{ number_format($totalPpn, 0, ',', '.') }}" readonly></div>
            <div class="col-12 d-flex flex-wrap gap-2 align-items-end">
                <button id="paySubmitBtn" class="btn {{ $isPaid ? 'btn-secondary' : 'btn-primary' }}" type="submit" {{ $isPaid ? 'disabled' : '' }}>
                    <i class="bi bi-cash-coin me-1"></i>Proses Bayar
                </button>
                @if($rows->isNotEmpty())
                    <button
                        type="button"
                        class="btn {{ $isPaid ? 'btn-info' : 'btn-secondary' }}"
                        data-receipt-url="{{ route('app.orders.receipt', ['id' => $order->id_order]) }}"
                        onclick="printReceipt(this.dataset.receiptUrl)"
                        {{ $isPaid ? '' : 'disabled' }}
                    >
                        <i class="bi bi-printer me-1"></i>Print Struk
                    </button>
                @endif
                @if($isPaid && (int) session('level_kantin', 0) === 1)
                    <form method="post" action="{{ route('app.orders.unpay', $order->id_order) }}" onsubmit="return confirm('Batalkan status bayar untuk order ini?')">
                        @csrf
                        <button class="btn btn-outline-warning" type="submit">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan Bayar
                        </button>
                    </form>
                @endif
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
                <span id="payHint" class="inline-field-msg ok d-none"></span>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const PRINT_MODE_KEY = 'kantin.print.mode';
    const QUICK_HISTORY_KEY = 'kantin.quick.menu.history';
    const QUICK_FAVORITE_KEY = 'kantin.quick.menu.count';
    const ORDER_TOTAL_BASE = Number(@json((float) $total));

    // Ambil mode print tersimpan dari localStorage.
    function getPrintMode() {
        const mode = localStorage.getItem(PRINT_MODE_KEY);
        return mode === 'popup' ? 'popup' : 'inline';
    }

    // Simpan preferensi mode print user.
    function setPrintMode(mode) {
        localStorage.setItem(PRINT_MODE_KEY, mode === 'popup' ? 'popup' : 'inline');
    }

    // Router aksi print: inline atau popup.
    function printReceipt(url) {
        if (getPrintMode() === 'popup') {
            openPrintPopup(url);
            return;
        }
        printReceiptInline(url);
    }

    // Cetak via window popup (fallback jika inline gagal).
    function openPrintPopup(url) {
        const popup = window.open(url + (url.includes('?') ? '&' : '?') + 'autoprint=1', 'print_struk', 'width=420,height=700,menubar=no,toolbar=no,location=no,status=no');
        if (popup) {
            popup.focus();
        }
    }

    // Cetak inline memakai iframe hidden agar UX lebih cepat.
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

    function loadQuickData(key, fallback) {
        try {
            const raw = localStorage.getItem(key);
            if (!raw) {
                return fallback;
            }
            const parsed = JSON.parse(raw);
            return parsed && typeof parsed === 'object' ? parsed : fallback;
        } catch (_e) {
            return fallback;
        }
    }

    function saveQuickData(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    }

    function isTypingField(target) {
        if (!target) {
            return false;
        }
        const tag = (target.tagName || '').toLowerCase();
        return tag === 'input' || tag === 'textarea' || tag === 'select' || target.isContentEditable;
    }

    function parseRupiah(value) {
        const raw = String(value || '').replace(/[^0-9]/g, '');
        if (!raw) {
            return 0;
        }
        return Number(raw);
    }

    function formatRupiah(value) {
        const amount = Math.max(0, Number(value || 0));
        return amount.toLocaleString('id-ID');
    }

    function setMenuSelection(menuSelect, menuId) {
        if (!menuSelect || !menuId) {
            return;
        }
        menuSelect.value = String(menuId);
        if (window.$ && $(menuSelect).hasClass('select2-hidden-accessible')) {
            $(menuSelect).trigger('change');
        } else {
            menuSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function renderQuickMenuButtons(menuMap, targetEl, ids, emptyText) {
        if (!targetEl) {
            return;
        }
        targetEl.innerHTML = '';
        const validIds = ids.filter(function (id) {
            return menuMap.has(String(id));
        });
        if (!validIds.length) {
            targetEl.innerHTML = '<span class="text-muted small">' + emptyText + '</span>';
            return;
        }

        validIds.forEach(function (id) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'quick-menu-btn';
            button.dataset.menuId = String(id);
            button.textContent = menuMap.get(String(id));
            targetEl.appendChild(button);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Sinkronkan dropdown mode print dengan preferensi user.
        const select = document.getElementById('printModeSelect');
        if (select) {
            select.value = getPrintMode();
            select.addEventListener('change', function () {
                setPrintMode(select.value);
            });
        }

        const addForm = document.getElementById('orderAddItemForm');
        const addMenuSelect = document.getElementById('addMenuSelect');
        const addQtyInput = document.getElementById('addQtyInput');
        const addItemSubmitBtn = document.getElementById('addItemSubmitBtn');
        const paySubmitBtn = document.getElementById('paySubmitBtn');
        const payForm = document.getElementById('orderPayForm');
        const discountInput = document.getElementById('discountInput');
        const payAmountInput = document.getElementById('payAmountInput');
        const discountError = document.getElementById('discountError');
        const payError = document.getElementById('payError');
        const payHint = document.getElementById('payHint');
        const recentMenuList = document.getElementById('recentMenuList');
        const favoriteMenuList = document.getElementById('favoriteMenuList');
        const clearQuickHistoryBtn = document.getElementById('clearQuickHistoryBtn');

        const menuMap = new Map();
        if (addMenuSelect) {
            Array.from(addMenuSelect.options).forEach(function (opt) {
                if (opt.value) {
                    menuMap.set(String(opt.value), opt.textContent.trim());
                }
            });
        }

        function renderQuickPanels() {
            const history = loadQuickData(QUICK_HISTORY_KEY, []);
            const favorites = loadQuickData(QUICK_FAVORITE_KEY, {});

            renderQuickMenuButtons(menuMap, recentMenuList, history.slice(0, 6), 'Belum ada riwayat pilihan menu.');

            const favoriteIds = Object.entries(favorites)
                .sort(function (a, b) {
                    return Number(b[1]) - Number(a[1]);
                })
                .map(function (row) {
                    return row[0];
                })
                .slice(0, 6);

            renderQuickMenuButtons(menuMap, favoriteMenuList, favoriteIds, 'Menu favorit akan muncul setelah beberapa transaksi.');
        }

        function rememberMenu(menuId) {
            if (!menuId) {
                return;
            }
            const history = loadQuickData(QUICK_HISTORY_KEY, []);
            const filtered = history.filter(function (id) {
                return String(id) !== String(menuId);
            });
            filtered.unshift(String(menuId));
            saveQuickData(QUICK_HISTORY_KEY, filtered.slice(0, 12));

            const favorites = loadQuickData(QUICK_FAVORITE_KEY, {});
            const current = Number(favorites[String(menuId)] || 0);
            favorites[String(menuId)] = current + 1;
            saveQuickData(QUICK_FAVORITE_KEY, favorites);
        }

        function handleQuickPick(event) {
            const button = event.target.closest('button[data-menu-id]');
            if (!button || !addMenuSelect) {
                return;
            }
            const menuId = button.dataset.menuId || '';
            setMenuSelection(addMenuSelect, menuId);
            if (addQtyInput) {
                addQtyInput.focus();
                addQtyInput.select();
            }
        }

        if (recentMenuList) {
            recentMenuList.addEventListener('click', handleQuickPick);
        }
        if (favoriteMenuList) {
            favoriteMenuList.addEventListener('click', handleQuickPick);
        }

        if (clearQuickHistoryBtn) {
            clearQuickHistoryBtn.addEventListener('click', function () {
                localStorage.removeItem(QUICK_HISTORY_KEY);
                localStorage.removeItem(QUICK_FAVORITE_KEY);
                renderQuickPanels();
            });
        }

        if (addForm) {
            addForm.addEventListener('submit', function () {
                if (!addMenuSelect) {
                    return;
                }
                rememberMenu(addMenuSelect.value);
            });
        }

        document.addEventListener('keydown', function (event) {
            const key = (event.key || '').toLowerCase();
            if ((event.ctrlKey || event.metaKey) && key === 's') {
                if (!paySubmitBtn || paySubmitBtn.disabled) {
                    return;
                }
                event.preventDefault();
                paySubmitBtn.click();
                return;
            }

            if (key === '/' && !isTypingField(event.target)) {
                if (!addMenuSelect) {
                    return;
                }
                event.preventDefault();
                if (window.$ && $(addMenuSelect).hasClass('select2-hidden-accessible')) {
                    $(addMenuSelect).select2('open');
                } else {
                    addMenuSelect.focus();
                }
                return;
            }

            if (key === 'enter' && !isTypingField(event.target)) {
                if (!addItemSubmitBtn || addItemSubmitBtn.disabled) {
                    return;
                }
                event.preventDefault();
                addItemSubmitBtn.click();
            }
        });

        function setFieldError(el, messageEl, message) {
            if (!el || !messageEl) {
                return;
            }
            if (!message) {
                el.classList.remove('is-invalid');
                messageEl.classList.add('d-none');
                messageEl.textContent = '';
                return;
            }
            el.classList.add('is-invalid');
            messageEl.classList.remove('d-none');
            messageEl.textContent = message;
        }

        function setPayHint(text) {
            if (!payHint) {
                return;
            }
            if (!text) {
                payHint.classList.add('d-none');
                payHint.textContent = '';
                return;
            }
            payHint.classList.remove('d-none');
            payHint.textContent = text;
        }

        function validatePayRealtime() {
            if (!payForm || !discountInput || !payAmountInput || !paySubmitBtn) {
                return true;
            }
            const discount = parseRupiah(discountInput.value);
            const pay = parseRupiah(payAmountInput.value);
            const maxDiscount = Math.max(0, Math.round(ORDER_TOTAL_BASE));
            const payable = Math.max(0, Math.ceil(ORDER_TOTAL_BASE - Math.min(discount, maxDiscount)));

            discountInput.value = formatRupiah(discount);
            payAmountInput.value = formatRupiah(pay);

            let hasError = false;
            if (discount > maxDiscount) {
                setFieldError(discountInput, discountError, 'Diskon tidak boleh melebihi total order.');
                hasError = true;
            } else {
                setFieldError(discountInput, discountError, '');
            }

            if (pay < payable) {
                setFieldError(payAmountInput, payError, 'Nominal bayar kurang dari total tagihan.');
                hasError = true;
            } else {
                setFieldError(payAmountInput, payError, '');
            }

            setPayHint('Tagihan saat ini: Rp ' + formatRupiah(payable));
            paySubmitBtn.disabled = hasError || paySubmitBtn.dataset.locked === '1';
            return !hasError;
        }

        if (discountInput) {
            discountInput.addEventListener('input', validatePayRealtime);
            discountInput.addEventListener('blur', validatePayRealtime);
        }
        if (payAmountInput) {
            payAmountInput.addEventListener('input', validatePayRealtime);
            payAmountInput.addEventListener('blur', validatePayRealtime);
        }
        if (payForm) {
            payForm.addEventListener('submit', function (event) {
                if (!validatePayRealtime()) {
                    event.preventDefault();
                }
            });
        }

        if (paySubmitBtn && paySubmitBtn.disabled) {
            paySubmitBtn.dataset.locked = '1';
        }
        validatePayRealtime();
        renderQuickPanels();
    });
</script>
@endpush
