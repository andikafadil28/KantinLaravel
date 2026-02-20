<?php

namespace App\Http\Controllers;

use App\Models\KantinBayar;
use App\Models\KantinKios;
use App\Models\KantinListOrder;
use App\Models\KantinMenu;
use App\Models\KantinOrder;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class KantinOrderController extends Controller
{
    public function index(Request $request): View
    {
        // Filter order berdasarkan level user dan kios aktif.
        $level = (int) $request->session()->get('level_kantin', 0);
        $sessionKios = (string) $request->session()->get('nama_toko_kantin', '');
        $filter = (string) $request->query('kios_filter', '');

        $query = KantinOrder::query()
            ->with(['kasirUser', 'pembayaran', 'items.menuRel'])
            ->orderByDesc('waktu_order')
            ->limit(250);

        if ($level === 3 && $sessionKios !== '') {
            $query->where('nama_kios', $sessionKios);
        } elseif ($filter !== '' && $filter !== 'all') {
            $query->where('nama_kios', $filter);
        }

        return view('app.order.index', [
            'orders' => $query->get(),
            'kios' => KantinKios::query()->orderBy('nama')->get(),
            'nextOrderCode' => date('ymdHi') . random_int(100, 999),
            'selectedKios' => $filter,
            'level' => $level,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi order baru.
        $data = $request->validate([
            'id_order' => ['required', 'integer'],
            'meja' => ['required', 'string', 'max:255'],
            'pelanggan' => ['required', 'string', 'max:200'],
            'nama_kios' => ['required', 'string', 'max:200'],
            'catatan' => ['nullable', 'string', 'max:200'],
        ]);

        if (KantinOrder::query()->where('id_order', $data['id_order'])->exists()) {
            return back()->withErrors(['id_order' => 'Kode order sudah terdaftar.']);
        }

        // Kasir diambil dari session agar tidak bisa dimanipulasi dari form.
        $data['kasir'] = (int) $request->session()->get('id_kantin');
        $data['catatan'] = $data['catatan'] ?? '';
        KantinOrder::query()->create($data);
        $this->writeOrderAuditLog(
            (int) $data['id_order'],
            'ORDER_CREATE',
            'Order dibuat untuk pelanggan ' . $data['pelanggan'] . ' (kios: ' . $data['nama_kios'] . ').',
            (int) $request->session()->get('id_kantin', 0),
            (string) $request->session()->get('username_kantin', 'unknown')
        );

        return redirect('/app/orders/' . $data['id_order']);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $order = KantinOrder::query()->findOrFail($id);
        $data = $request->validate([
            'meja' => ['required', 'string', 'max:255'],
            'pelanggan' => ['required', 'string', 'max:200'],
            'nama_kios' => ['required', 'string', 'max:200'],
            'catatan' => ['nullable', 'string', 'max:200'],
        ]);

        $order->update([
            'meja' => $data['meja'],
            'pelanggan' => $data['pelanggan'],
            'nama_kios' => $data['nama_kios'],
            'catatan' => $data['catatan'] ?? '',
        ]);
        $this->writeOrderAuditLog(
            (int) $order->id_order,
            'ORDER_UPDATE',
            'Order diperbarui. Pelanggan: ' . $data['pelanggan'] . ', meja: ' . $data['meja'] . '.',
            (int) $request->session()->get('id_kantin', 0),
            (string) $request->session()->get('username_kantin', 'unknown')
        );

        return back()->with('ok', 'Order berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $order = KantinOrder::query()->findOrFail($id);
        $actorId = (int) session('id_kantin', 0);
        $actorUsername = (string) session('username_kantin', 'unknown');
        if ($order->items()->exists()) {
            return back()->withErrors(['order' => 'Order tidak dapat dihapus karena masih memiliki item.']);
        }

        $order->delete();
        $this->writeOrderAuditLog(
            (int) $id,
            'ORDER_DELETE',
            'Order dihapus.',
            $actorId,
            $actorUsername
        );

        return back()->with('ok', 'Order berhasil dihapus.');
    }

    public function show(Request $request, int $id): View
    {
        $order = KantinOrder::query()
            ->with(['items.menuRel', 'pembayaran'])
            ->findOrFail($id);

        // Hanya admin yang boleh ubah order setelah dibayar.
        $level = (int) $request->session()->get('level_kantin', 0);
        $canEditPaid = $level === 1;
        $isPaid = $order->pembayaran !== null;
        $canModify = $canEditPaid || !$isPaid;

        $summary = $this->buildOrderSummary($order);
        $rows = $summary['rows'];
        $total = $summary['total'];
        $totalPpn = $summary['totalPpn'];
        $totalToko = $summary['totalToko'];
        $diskonExisting = (float) round($order->pembayaran?->diskon ?? 0, 0);
        $grandTotal = max(0, (float) ceil($total - $diskonExisting));

        return view('app.order.show', [
            'order' => $order,
            'rows' => $rows,
            'menus' => KantinMenu::query()
                ->where('nama_toko', $order->nama_kios)
                ->where('status', 1)
                ->orderBy('nama')
                ->get(),
            'total' => $total,
            'totalPpn' => $totalPpn,
            'totalToko' => $totalToko,
            'diskonExisting' => $diskonExisting,
            'grandTotal' => $grandTotal,
            'isPaid' => $isPaid,
            'canModify' => $canModify,
        ]);
    }

    public function receipt(Request $request, int $id): View
    {
        $order = KantinOrder::query()
            ->with(['items.menuRel', 'pembayaran', 'kasirUser'])
            ->findOrFail($id);

        $summary = $this->buildOrderSummary($order);
        $rows = $summary['rows'];
        $total = $summary['total'];
        $totalPpn = $summary['totalPpn'];
        $diskon = (float) round($order->pembayaran?->diskon ?? 0, 0);
        $grandTotal = max(0, (float) ceil($total - $diskon));
        $bayar = (float) round($order->pembayaran?->nominal_uang ?? 0, 0);
        $kembalian = max(0, (float) round($bayar - $grandTotal, 0));

        return view('app.order.receipt', [
            'order' => $order,
            'rows' => $rows,
            'total' => $total,
            'totalPpn' => $totalPpn,
            'diskon' => $diskon,
            'grandTotal' => $grandTotal,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'autoPrint' => $request->boolean('autoprint'),
        ]);
    }

    public function addItem(Request $request, int $id): RedirectResponse
    {
        $order = KantinOrder::query()->with('pembayaran')->findOrFail($id);
        // Cegah perubahan item jika order sudah lunas.
        if ($order->pembayaran) {
            return back()->withErrors(['item' => 'Order sudah dibayar, item tidak dapat ditambah.']);
        }

        $data = $request->validate([
            'menu' => ['required', 'integer'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan_order' => ['nullable', 'string', 'max:255'],
        ]);

        $exists = KantinListOrder::query()
            ->where('kode_order', $order->id_order)
            ->where('menu', $data['menu'])
            ->exists();
        // Cegah duplikasi menu pada satu order.
        if ($exists) {
            return back()->withErrors(['item' => 'Item sudah terdaftar dalam order ini.']);
        }

        KantinListOrder::query()->create([
            'kode_order' => $order->id_order,
            'menu' => $data['menu'],
            'jumlah' => $data['jumlah'],
            'catatan_order' => $data['catatan_order'] ?? '',
            'status' => '0',
        ]);
        $this->writeOrderAuditLog(
            (int) $order->id_order,
            'ITEM_ADD',
            'Tambah item menu #' . $data['menu'] . ' qty ' . $data['jumlah'] . '.',
            (int) $request->session()->get('id_kantin', 0),
            (string) $request->session()->get('username_kantin', 'unknown')
        );

        return back()->with('ok', 'Item berhasil ditambahkan.');
    }

    public function updateItem(Request $request, int $id, int $itemId): RedirectResponse
    {
        $order = KantinOrder::query()->with('pembayaran')->findOrFail($id);
        $item = KantinListOrder::query()
            ->where('kode_order', $order->id_order)
            ->findOrFail($itemId);

        $level = (int) $request->session()->get('level_kantin', 0);
        if ($level !== 1 && $order->pembayaran) {
            return back()->withErrors(['item' => 'Order sudah dibayar, item tidak dapat diubah.']);
        }

        $data = $request->validate([
            'menu' => ['required', 'integer'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan_order' => ['nullable', 'string', 'max:255'],
        ]);

        $duplicate = KantinListOrder::query()
            ->where('kode_order', $order->id_order)
            ->where('menu', $data['menu'])
            ->where('id_list_order', '<>', $item->id_list_order)
            ->exists();
        if ($duplicate) {
            return back()->withErrors(['item' => 'Menu yang sama sudah ada di order ini.']);
        }

        $item->update([
            'menu' => $data['menu'],
            'jumlah' => $data['jumlah'],
            'catatan_order' => $data['catatan_order'] ?? '',
        ]);
        $this->writeOrderAuditLog(
            (int) $order->id_order,
            'ITEM_UPDATE',
            'Update item #' . $item->id_list_order . ' ke menu #' . $data['menu'] . ' qty ' . $data['jumlah'] . '.',
            (int) $request->session()->get('id_kantin', 0),
            (string) $request->session()->get('username_kantin', 'unknown')
        );

        return back()->with('ok', 'Item berhasil diperbarui.');
    }

    public function deleteItem(Request $request, int $id, int $itemId): RedirectResponse
    {
        $order = KantinOrder::query()->with('pembayaran')->findOrFail($id);
        $item = KantinListOrder::query()
            ->where('kode_order', $order->id_order)
            ->findOrFail($itemId);

        $level = (int) $request->session()->get('level_kantin', 0);
        if ($level !== 1 && $order->pembayaran) {
            return back()->withErrors(['item' => 'Order sudah dibayar, item tidak dapat dihapus.']);
        }

        $deletedItemId = (int) $item->id_list_order;
        $item->delete();
        $this->writeOrderAuditLog(
            (int) $order->id_order,
            'ITEM_DELETE',
            'Hapus item #' . $deletedItemId . '.',
            (int) $request->session()->get('id_kantin', 0),
            (string) $request->session()->get('username_kantin', 'unknown')
        );

        return back()->with('ok', 'Item berhasil dihapus.');
    }

    public function clearItems(Request $request, int $id): RedirectResponse
    {
        $order = KantinOrder::query()->with('pembayaran')->findOrFail($id);

        // Hapus semua item hanya untuk order yang belum dibayar.
        if ($order->pembayaran) {
            return back()->withErrors(['item' => 'Order sudah dibayar. Batalkan pembayaran terlebih dahulu sebelum menghapus semua item.']);
        }

        $deleted = KantinListOrder::query()
            ->where('kode_order', $order->id_order)
            ->delete();

        if ($deleted <= 0) {
            return back()->withErrors(['item' => 'Tidak ada item untuk dihapus.']);
        }

        $this->writeOrderAuditLog(
            (int) $order->id_order,
            'ITEM_CLEAR',
            'Hapus semua item order. Jumlah item terhapus: ' . $deleted . '.',
            (int) $request->session()->get('id_kantin', 0),
            (string) $request->session()->get('username_kantin', 'unknown')
        );

        return back()->with('ok', 'Semua item order berhasil dihapus.');
    }

    public function pay(Request $request, int $id): RedirectResponse
    {
        $order = KantinOrder::query()->with(['items.menuRel', 'pembayaran'])->findOrFail($id);
        if ($order->pembayaran) {
            return back()->withErrors(['pay' => 'Order ini sudah dibayar.']);
        }

        $request->validate([
            'diskon' => ['nullable', 'string', 'max:32'],
            'bayar' => ['required', 'string', 'max:32'],
        ]);

        // Hitung total berdasarkan harga menu + pajak menu legacy.
        $total = 0.0;
        $totalPpn = 0.0;
        $totalToko = 0.0;
        foreach ($order->items as $item) {
            $harga = (float) ($item->menuRel?->harga ?? 0);
            $pajak = (float) ($item->menuRel?->pajak ?? 0);
            $qty = (int) $item->jumlah;
            $total += (($harga + $pajak) * 1.11) * $qty;
            $totalPpn += (($harga + $pajak) * 0.11) * $qty;
            $totalToko += $harga * $qty;
        }

        $total = round($total, 0);
        $totalPpn = round($totalPpn, 0);
        $totalToko = round($totalToko, 0);

        $diskon = min(max(round($this->parseMoneyInput($request->input('diskon', '0')), 0), 0), $total);
        $grandTotal = max(0, (float) ceil($total - $diskon));
        $bayar = $this->parseMoneyInput((string) $request->input('bayar', '0'));
        // Validasi nominal bayar harus cukup.
        if ($bayar < $grandTotal) {
            return back()->withErrors([
                'pay' => 'Jumlah bayar tidak cukup. Total tagihan Rp ' . number_format($grandTotal, 0, ',', '.'),
            ])->withInput();
        }

        // Simpan ringkasan pembayaran final ke tb_bayar.
        $hargaTokoFinal = max(0, (float) round($totalToko - $diskon, 0));
        $nominalRs = $grandTotal - $hargaTokoFinal;
        KantinBayar::query()->create([
            'id_bayar' => $order->id_order,
            'nominal_uang' => $bayar,
            'jumlah_bayar' => $grandTotal,
            'ppn' => $totalPpn,
            'nominal_toko' => $hargaTokoFinal,
            'nominal_rs' => $nominalRs,
            'diskon' => $diskon,
            'kode_order_bayar' => $order->id_order,
        ]);

        KantinListOrder::query()->where('kode_order', $order->id_order)->update([
            'status' => 'Lunas',
        ]);
        $this->writeOrderAuditLog(
            (int) $order->id_order,
            'PAY',
            'Pembayaran diproses. Grand total: ' . $grandTotal . ', bayar: ' . $bayar . ', diskon: ' . $diskon . '.',
            (int) $request->session()->get('id_kantin', 0),
            (string) $request->session()->get('username_kantin', 'unknown')
        );

        $kembalian = $bayar - $grandTotal;

        return back()->with('ok', 'Pembayaran berhasil. Kembalian: Rp ' . number_format($kembalian, 0, ',', '.'));
    }

    public function unpay(Request $request, int $id): RedirectResponse
    {
        // Pembatalan bayar hanya boleh dilakukan admin.
        if ((int) $request->session()->get('level_kantin', 0) !== 1) {
            abort(403, 'Hanya admin yang dapat membatalkan pembayaran.');
        }

        $order = KantinOrder::query()->with('pembayaran')->findOrFail($id);
        if (!$order->pembayaran) {
            return back()->withErrors(['pay' => 'Order ini belum memiliki data pembayaran.']);
        }

        $paySnapshot = [
            'jumlah_bayar' => (float) ($order->pembayaran->jumlah_bayar ?? 0),
            'nominal_uang' => (float) ($order->pembayaran->nominal_uang ?? 0),
            'diskon' => (float) ($order->pembayaran->diskon ?? 0),
        ];
        $actorId = (int) $request->session()->get('id_kantin', 0);
        $actorUsername = (string) $request->session()->get('username_kantin', 'unknown');

        DB::transaction(function () use ($order): void {
            KantinBayar::query()->where('id_bayar', $order->id_order)->delete();
            KantinListOrder::query()
                ->where('kode_order', $order->id_order)
                ->update(['status' => '0']);
        });

        $this->writeOrderAuditLog(
            $order->id_order,
            'UNPAY',
            'Pembayaran dibatalkan. Snapshot sebelum batal: ' . json_encode($paySnapshot, JSON_UNESCAPED_UNICODE),
            $actorId,
            $actorUsername
        );

        return back()->with('ok', 'Pembayaran berhasil dibatalkan.');
    }

    private function writeOrderAuditLog(int $orderId, string $action, string $description, int $actorId, string $actorUsername): void
    {
        if (!Schema::hasTable('tb_audit_kantin')) {
            return;
        }

        DB::table('tb_audit_kantin')->insert([
            'order_id' => $orderId,
            'action' => $action,
            'description' => $description,
            'actor_id' => $actorId > 0 ? $actorId : null,
            'actor_username' => $actorUsername !== '' ? $actorUsername : null,
            'created_at' => now(),
        ]);
    }

    private function parseMoneyInput(string $value): float
    {
        // Normalisasi berbagai format input rupiah (Rp, titik ribuan, koma).
        $normalized = Str::of($value)
            ->replace('Rp', '')
            ->replace([' ', "\u{00A0}"], '')
            ->replace(',', '.')
            ->toString();

        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $normalized)) {
            $normalized = str_replace('.', '', $normalized);
        }

        $normalized = preg_replace('/[^0-9.]/', '', $normalized) ?? '0';
        if ($normalized === '' || substr_count($normalized, '.') > 1) {
            return 0.0;
        }

        return (float) $normalized;
    }

    private function buildOrderSummary(KantinOrder $order): array
    {
        // Hitung subtotal, PPN, dan komponen toko per item.
        $rows = $order->items->map(function (KantinListOrder $item): array {
            $menu = $item->menuRel;
            $harga = (float) ($menu?->harga ?? 0);
            $pajak = (float) ($menu?->pajak ?? 0);
            $qty = (int) $item->jumlah;
            $hargaJual = ($harga + $pajak) * 1.11;
            $subtotal = $hargaJual * $qty;
            $ppn = (($harga + $pajak) * 0.11) * $qty;
            $hargaToko = $harga * $qty;

            return [
                'item' => $item,
                'menu' => $menu,
                'harga_jual' => $hargaJual,
                'subtotal' => $subtotal,
                'ppn' => $ppn,
                'harga_toko' => $hargaToko,
            ];
        });

        return [
            'rows' => $rows,
            'total' => (float) round($rows->sum('subtotal'), 0),
            'totalPpn' => (float) round($rows->sum('ppn'), 0),
            'totalToko' => (float) round($rows->sum('harga_toko'), 0),
        ];
    }
}
