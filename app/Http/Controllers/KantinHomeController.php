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
        $userId = (int) $request->session()->get('kantin_user_id');
        $user = KantinUser::query()->find($userId);

        $openOrders = KantinOrder::query()
            ->where('kasir', $userId)
            ->whereDate('waktu_order', now()->toDateString())
            ->count();

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

        return view('app.home', [
            'user' => $user,
            'openOrders' => $openOrders,
            'dailyMenuLabels' => $dailyRows->pluck('nama_menu')->values(),
            'dailyMenuTotals' => $dailyRows->pluck('total_terjual')->map(fn ($v) => (int) $v)->values(),
            'weeklyMenuLabels' => $weeklyRows->pluck('nama_menu')->values(),
            'weeklyMenuTotals' => $weeklyRows->pluck('total_terjual')->map(fn ($v) => (int) $v)->values(),
        ]);
    }
}
