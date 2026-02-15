<?php

namespace App\Http\Controllers;

use App\Models\KantinOrder;
use App\Models\KantinUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KantinHomeController extends Controller
{
    public function index(Request $request): View
    {
        // Ambil konteks user login dari session legacy/app.
        $userId = (int) $request->session()->get('kantin_user_id');
        $user = KantinUser::query()->find($userId);
        $level = (int) $request->session()->get('level_kantin', 0);
        $sessionKios = (string) $request->session()->get('nama_toko_kantin', '');
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Order aktif kasir login pada hari ini.
        $openOrders = KantinOrder::query()
            ->where('kasir', $userId)
            ->whereDate('waktu_order', $today)
            ->count();

        // Scope data order: jika level kios, batasi hanya kios miliknya.
        $todayOrderScope = DB::table('tb_order')
            ->when($level === 3 && $sessionKios !== '', fn ($q) => $q->where('tb_order.nama_kios', $sessionKios));

        // Ringkasan transaksi harian untuk KPI dashboard.
        $todayOrderCount = (clone $todayOrderScope)
            ->whereDate('tb_order.waktu_order', $today)
            ->count();

        $todayRevenue = (float) ((clone $todayOrderScope)
            ->leftJoin('tb_bayar', 'tb_bayar.id_bayar', '=', 'tb_order.id_order')
            ->whereDate('tb_order.waktu_order', $today)
            ->sum('tb_bayar.jumlah_bayar'));

        $yesterdayRevenue = (float) (DB::table('tb_order')
            ->when($level === 3 && $sessionKios !== '', fn ($q) => $q->where('tb_order.nama_kios', $sessionKios))
            ->leftJoin('tb_bayar', 'tb_bayar.id_bayar', '=', 'tb_order.id_order')
            ->whereDate('tb_order.waktu_order', $yesterday)
            ->sum('tb_bayar.jumlah_bayar'));

        // Hitung arah tren omzet terhadap hari sebelumnya.
        $trendDiff = $todayRevenue - $yesterdayRevenue;
        $trendPercent = $yesterdayRevenue > 0
            ? round(($trendDiff / $yesterdayRevenue) * 100, 1)
            : null;
        $trendDirection = $trendDiff > 0 ? 'up' : ($trendDiff < 0 ? 'down' : 'flat');

        // Top kios hari ini untuk insight cepat di dashboard.
        $topKiosToday = DB::table('tb_order')
            ->leftJoin('tb_bayar', 'tb_bayar.id_bayar', '=', 'tb_order.id_order')
            ->selectRaw('tb_order.nama_kios, COUNT(tb_order.id_order) as total_order, COALESCE(SUM(tb_bayar.jumlah_bayar), 0) as total_omzet')
            ->whereDate('tb_order.waktu_order', $today)
            ->when($level === 3 && $sessionKios !== '', fn ($q) => $q->where('tb_order.nama_kios', $sessionKios))
            ->groupBy('tb_order.nama_kios')
            ->orderByDesc('total_order')
            ->orderByDesc('total_omzet')
            ->limit(3)
            ->get();

        // Dataset chart menu terlaris hari ini.
        $dailyRows = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama as nama_menu, SUM(tb_list_order.jumlah) as total_terjual')
            ->whereNotNull('tb_menu.nama')
            ->whereDate('tb_order.waktu_order', now()->toDateString())
            ->groupBy('tb_menu.nama', 'tb_menu.nama_toko')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // Dataset chart menu terlaris minggu berjalan.
        $weeklyRows = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama as nama_menu, SUM(tb_list_order.jumlah) as total_terjual')
            ->whereNotNull('tb_menu.nama')
            ->whereRaw('YEARWEEK(tb_order.waktu_order, 1) = YEARWEEK(CURDATE(), 1)')
            ->groupBy('tb_menu.nama', 'tb_menu.nama_toko')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // Kirim semua metrik agar view bisa render dashboard lengkap.
        return view('app.home', [
            'user' => $user,
            'openOrders' => $openOrders,
            'todayOrderCount' => (int) $todayOrderCount,
            'todayRevenue' => (float) $todayRevenue,
            'yesterdayRevenue' => (float) $yesterdayRevenue,
            'trendDiff' => (float) $trendDiff,
            'trendPercent' => $trendPercent,
            'trendDirection' => $trendDirection,
            'topKiosToday' => $topKiosToday,
            'dailyMenuLabels' => $dailyRows->pluck('nama_menu')->values(),
            'dailyMenuTotals' => $dailyRows->pluck('total_terjual')->map(fn ($v) => (int) $v)->values(),
            'weeklyMenuLabels' => $weeklyRows->pluck('nama_menu')->values(),
            'weeklyMenuTotals' => $weeklyRows->pluck('total_terjual')->map(fn ($v) => (int) $v)->values(),
        ]);
    }
}
