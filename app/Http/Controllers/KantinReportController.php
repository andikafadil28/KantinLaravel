<?php

namespace App\Http\Controllers;

use App\Models\KantinKios;
use App\Models\KantinOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KantinReportController extends Controller
{
    private const REKAP_RS_MENU_TARGETS = [
        'Es Teh',
        'Es Jeruk',
        'Es Milo',
        'Es Susu',
        'Es Coffe Mix',
        'Air Putih',
        'Nutrisari',
        'Es Good Day',
    ];

    public function orders(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();

        return view('app.report.orders', [
            'rows' => $rows,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
            'sumToko' => (float) $rows->sum('nominal_toko'),
            'sumRs' => (float) $rows->sum('nominal_rs'),
            'sumDiskon' => (float) $rows->sum('diskon'),
        ]);
    }

    public function rs(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();

        return view('app.report.rs', [
            'rows' => $rows,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
            'sumRs' => (float) $rows->sum('nominal_rs'),
        ]);
    }

    public function toko(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();

        return view('app.report.toko', [
            'rows' => $rows,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
            'sumToko' => (float) $rows->sum('nominal_toko'),
            'sumDiskon' => (float) $rows->sum('diskon'),
        ]);
    }

    public function menuSales(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama as nama_menu, tb_menu.nama_toko, SUM(tb_list_order.jumlah) as total_terjual, tb_menu.harga as harga_satuan, SUM(tb_list_order.jumlah * tb_menu.harga) as total_harga')
            ->groupBy('tb_menu.nama', 'tb_menu.nama_toko', 'tb_menu.harga')
            ->orderBy('tb_menu.nama_toko')
            ->orderBy('tb_menu.nama');

        if ($start) {
            $query->where('tb_order.waktu_order', '>=', $start . ' 00:00:00');
        }
        if ($end) {
            $query->where('tb_order.waktu_order', '<=', $end . ' 23:59:59');
        }
        if ($kios && $kios !== 'all') {
            $query->where('tb_order.nama_kios', $kios);
        }

        $rows = $query->get();

        return view('app.report.menu', [
            'rows' => $rows,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
            'sumTotal' => (float) $rows->sum('total_harga'),
        ]);
    }

    public function rekapRs(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama, SUM(tb_list_order.jumlah) AS total_terjual, tb_menu.pajak AS harga_satuan, SUM(tb_list_order.jumlah) * tb_menu.pajak AS total_harga, tb_menu.nama_toko')
            ->groupBy('tb_menu.nama', 'tb_menu.nama_toko', 'tb_menu.pajak')
            ->orderBy('tb_menu.nama_toko')
            ->orderBy('tb_menu.nama');

        $this->applyOrderFilter($query, $start, $end, $kios);
        $rows = $query->get();

        return view('app.report.rekap_rs', [
            'rows' => $rows,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
            'sumTotal' => (float) $rows->sum('total_harga'),
        ]);
    }

    public function rekapMenuRs(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama, SUM(tb_list_order.jumlah) AS total_terjual, tb_menu.pajak AS harga_satuan, SUM(tb_list_order.jumlah) * tb_menu.pajak AS total_harga')
            ->whereIn('tb_menu.nama', self::REKAP_RS_MENU_TARGETS)
            ->groupBy('tb_menu.nama', 'tb_menu.pajak')
            ->orderBy('tb_menu.nama');

        $this->applyOrderFilter($query, $start, $end, $kios);
        $rows = $query->get();

        return view('app.report.rekap_menu_rs', [
            'rows' => $rows,
            'targetMenus' => self::REKAP_RS_MENU_TARGETS,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
            'sumTotal' => (float) $rows->sum('total_harga'),
        ]);
    }

    public function financeDetail(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_list_order')
            ->rightJoin('tb_order', 'tb_order.id_order', '=', 'tb_list_order.kode_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw("
                tb_order.waktu_order AS waktu_order,
                tb_menu.nama AS nama_menu,
                tb_list_order.jumlah AS jumlah_terjual,
                tb_order.nama_kios AS nama_toko,
                (tb_menu.harga + tb_menu.pajak) AS harga_jual_per_menu,
                (tb_menu.harga + tb_menu.pajak) * 0.11 AS harga_ppn,
                (tb_menu.harga + tb_menu.pajak) + ((tb_menu.harga + tb_menu.pajak) * 0.11) AS harga_pembeli_per_menu,
                (tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah AS harga_total_per_menu,
                ((tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah) * 0.11 AS harga_total_ppn,
                ((tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah) + (((tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah) * 0.11) AS harga_pembeli_total,
                tb_menu.harga * tb_list_order.jumlah AS keuntungan_toko,
                tb_menu.pajak * tb_list_order.jumlah AS keuntungan_rs,
                (tb_menu.pajak * tb_list_order.jumlah) + (((tb_menu.harga + tb_menu.pajak) * 0.11) * tb_list_order.jumlah) AS keuntungan_rs_pajak
            ")
            ->orderByDesc('tb_order.waktu_order');

        $this->applyOrderFilter($query, $start, $end, $kios);
        $rows = $query->get();

        return view('app.report.finance_detail', [
            'rows' => $rows,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
        ]);
    }

    public function financeMenu(Request $request): View
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_list_order')
            ->rightJoin('tb_order', 'tb_order.id_order', '=', 'tb_list_order.kode_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw("
                tb_menu.nama AS nama_menu,
                SUM(tb_list_order.jumlah) AS jumlah_terjual,
                tb_order.nama_kios AS nama_toko,
                (tb_menu.harga + tb_menu.pajak) AS harga_jual_per_menu,
                (tb_menu.harga + tb_menu.pajak) * 0.11 AS harga_ppn,
                (tb_menu.harga + tb_menu.pajak) + ((tb_menu.harga + tb_menu.pajak) * 0.11) AS harga_pembeli_per_menu,
                (tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah) AS harga_total_per_menu,
                ((tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah)) * 0.11 AS harga_total_ppn,
                ((tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah)) + (((tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah)) * 0.11) AS harga_pembeli_total,
                tb_menu.harga * SUM(tb_list_order.jumlah) AS keuntungan_toko,
                tb_menu.pajak * SUM(tb_list_order.jumlah) AS keuntungan_rs,
                (tb_menu.pajak * SUM(tb_list_order.jumlah)) + (((tb_menu.harga + tb_menu.pajak) * 0.11) * SUM(tb_list_order.jumlah)) AS keuntungan_rs_pajak
            ")
            ->groupBy('tb_menu.nama', 'tb_order.nama_kios', 'tb_menu.harga', 'tb_menu.pajak')
            ->orderBy('tb_order.nama_kios')
            ->orderBy('tb_menu.nama');

        $this->applyOrderFilter($query, $start, $end, $kios);
        $rows = $query->get();

        return view('app.report.finance_menu', [
            'rows' => $rows,
            'kiosList' => KantinKios::query()->orderBy('nama')->get(),
            'start' => $start,
            'end' => $end,
            'kios' => $kios,
        ]);
    }

    public function exportOrdersCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();

        return $this->csvResponse('laporan_detail.csv', [
            'kode_order', 'pelanggan', 'meja', 'nominal_toko', 'nominal_rs', 'jumlah_bayar', 'diskon', 'waktu_order', 'nama_kios',
        ], $rows->map(fn ($r) => [
            $r->id_order, $r->pelanggan, $r->meja, $r->nominal_toko, $r->nominal_rs, $r->jumlah_bayar, $r->diskon, $r->waktu_order, $r->nama_kios,
        ]));
    }

    public function exportRsCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();

        return $this->csvResponse('laporan_rs.csv', [
            'kode_order', 'pelanggan', 'meja', 'nominal_rs', 'waktu_order', 'nama_kios',
        ], $rows->map(fn ($r) => [
            $r->id_order, $r->pelanggan, $r->meja, $r->nominal_rs, $r->waktu_order, $r->nama_kios,
        ]));
    }

    public function exportTokoCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();

        return $this->csvResponse('laporan_toko.csv', [
            'kode_order', 'pelanggan', 'meja', 'nominal_toko', 'diskon', 'waktu_order', 'nama_kios',
        ], $rows->map(fn ($r) => [
            $r->id_order, $r->pelanggan, $r->meja, $r->nominal_toko, $r->diskon, $r->waktu_order, $r->nama_kios,
        ]));
    }

    public function exportMenuCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama as nama_menu, tb_menu.nama_toko, SUM(tb_list_order.jumlah) as total_terjual, tb_menu.harga as harga_satuan, SUM(tb_list_order.jumlah * tb_menu.harga) as total_harga')
            ->groupBy('tb_menu.nama', 'tb_menu.nama_toko', 'tb_menu.harga');
        if ($start) {
            $query->where('tb_order.waktu_order', '>=', $start . ' 00:00:00');
        }
        if ($end) {
            $query->where('tb_order.waktu_order', '<=', $end . ' 23:59:59');
        }
        if ($kios && $kios !== 'all') {
            $query->where('tb_order.nama_kios', $kios);
        }
        $rows = $query->get();

        return $this->csvResponse('laporan_menu.csv', [
            'nama_menu', 'nama_toko', 'total_terjual', 'harga_satuan', 'total_harga',
        ], $rows->map(fn ($r) => [
            $r->nama_menu, $r->nama_toko, $r->total_terjual, $r->harga_satuan, $r->total_harga,
        ]));
    }

    public function exportRekapRsCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama, SUM(tb_list_order.jumlah) AS total_terjual, tb_menu.pajak AS harga_satuan, SUM(tb_list_order.jumlah) * tb_menu.pajak AS total_harga, tb_menu.nama_toko')
            ->groupBy('tb_menu.nama', 'tb_menu.nama_toko', 'tb_menu.pajak')
            ->orderBy('tb_menu.nama_toko')
            ->orderBy('tb_menu.nama');
        $this->applyOrderFilter($query, $start, $end, $kios);
        $rows = $query->get();

        return $this->csvResponse('laporan_rekap_rs.csv', [
            'nama_menu', 'nama_toko', 'total_terjual', 'harga_satuan', 'total_harga',
        ], $rows->map(fn ($r) => [
            $r->nama, $r->nama_toko, $r->total_terjual, $r->harga_satuan, $r->total_harga,
        ]));
    }

    public function exportRekapMenuRsCsv(Request $request)
    {
        [$start, $end] = $this->filters($request);
        $query = DB::table('tb_order')
            ->leftJoin('tb_list_order', 'tb_list_order.kode_order', '=', 'tb_order.id_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw('tb_menu.nama, SUM(tb_list_order.jumlah) AS total_terjual, tb_menu.pajak AS harga_satuan, SUM(tb_list_order.jumlah) * tb_menu.pajak AS total_harga')
            ->whereIn('tb_menu.nama', self::REKAP_RS_MENU_TARGETS)
            ->groupBy('tb_menu.nama', 'tb_menu.pajak')
            ->orderBy('tb_menu.nama');
        $this->applyOrderFilter($query, $start, $end, '');
        $rows = $query->get();

        return $this->csvResponse('laporan_rekap_menu_rs.csv', [
            'nama_menu', 'total_terjual', 'harga_satuan', 'total_harga',
        ], $rows->map(fn ($r) => [
            $r->nama, $r->total_terjual, $r->harga_satuan, $r->total_harga,
        ]));
    }

    public function exportFinanceDetailCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_list_order')
            ->rightJoin('tb_order', 'tb_order.id_order', '=', 'tb_list_order.kode_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw("
                tb_order.waktu_order AS waktu_order,
                tb_menu.nama AS nama_menu,
                tb_list_order.jumlah AS jumlah_terjual,
                tb_order.nama_kios AS nama_toko,
                (tb_menu.harga + tb_menu.pajak) AS harga_jual_per_menu,
                (tb_menu.harga + tb_menu.pajak) * 0.11 AS harga_ppn,
                (tb_menu.harga + tb_menu.pajak) + ((tb_menu.harga + tb_menu.pajak) * 0.11) AS harga_pembeli_per_menu,
                (tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah AS harga_total_per_menu,
                ((tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah) * 0.11 AS harga_total_ppn,
                ((tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah) + (((tb_menu.harga + tb_menu.pajak) * tb_list_order.jumlah) * 0.11) AS harga_pembeli_total,
                tb_menu.harga * tb_list_order.jumlah AS keuntungan_toko,
                tb_menu.pajak * tb_list_order.jumlah AS keuntungan_rs,
                (tb_menu.pajak * tb_list_order.jumlah) + (((tb_menu.harga + tb_menu.pajak) * 0.11) * tb_list_order.jumlah) AS keuntungan_rs_pajak
            ")
            ->orderByDesc('tb_order.waktu_order');
        $this->applyOrderFilter($query, $start, $end, $kios);
        $rows = $query->get();

        return $this->csvResponse('laporan_finance_detail.csv', [
            'waktu_order', 'nama_menu', 'jumlah', 'nama_toko', 'harga_jual', 'ppn', 'harga_pembeli', 'total_menu', 'total_ppn', 'total_pembeli', 'untung_toko', 'untung_rs', 'untung_rs_pajak',
        ], $rows->map(fn ($r) => [
            $r->waktu_order, $r->nama_menu, $r->jumlah_terjual, $r->nama_toko, $r->harga_jual_per_menu, $r->harga_ppn, $r->harga_pembeli_per_menu, $r->harga_total_per_menu, $r->harga_total_ppn, $r->harga_pembeli_total, $r->keuntungan_toko, $r->keuntungan_rs, $r->keuntungan_rs_pajak,
        ]));
    }

    public function exportFinanceMenuCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $query = DB::table('tb_list_order')
            ->rightJoin('tb_order', 'tb_order.id_order', '=', 'tb_list_order.kode_order')
            ->leftJoin('tb_menu', 'tb_menu.id', '=', 'tb_list_order.menu')
            ->selectRaw("
                tb_menu.nama AS nama_menu,
                SUM(tb_list_order.jumlah) AS jumlah_terjual,
                tb_order.nama_kios AS nama_toko,
                (tb_menu.harga + tb_menu.pajak) AS harga_jual_per_menu,
                (tb_menu.harga + tb_menu.pajak) * 0.11 AS harga_ppn,
                (tb_menu.harga + tb_menu.pajak) + ((tb_menu.harga + tb_menu.pajak) * 0.11) AS harga_pembeli_per_menu,
                (tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah) AS harga_total_per_menu,
                ((tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah)) * 0.11 AS harga_total_ppn,
                ((tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah)) + (((tb_menu.harga + tb_menu.pajak) * SUM(tb_list_order.jumlah)) * 0.11) AS harga_pembeli_total,
                tb_menu.harga * SUM(tb_list_order.jumlah) AS keuntungan_toko,
                tb_menu.pajak * SUM(tb_list_order.jumlah) AS keuntungan_rs,
                (tb_menu.pajak * SUM(tb_list_order.jumlah)) + (((tb_menu.harga + tb_menu.pajak) * 0.11) * SUM(tb_list_order.jumlah)) AS keuntungan_rs_pajak
            ")
            ->groupBy('tb_menu.nama', 'tb_order.nama_kios', 'tb_menu.harga', 'tb_menu.pajak')
            ->orderBy('tb_order.nama_kios')
            ->orderBy('tb_menu.nama');
        $this->applyOrderFilter($query, $start, $end, $kios);
        $rows = $query->get();

        return $this->csvResponse('laporan_finance_menu.csv', [
            'nama_menu', 'jumlah', 'nama_toko', 'harga_jual', 'ppn', 'harga_pembeli', 'total_menu', 'total_ppn', 'total_pembeli', 'untung_toko', 'untung_rs', 'untung_rs_pajak',
        ], $rows->map(fn ($r) => [
            $r->nama_menu, $r->jumlah_terjual, $r->nama_toko, $r->harga_jual_per_menu, $r->harga_ppn, $r->harga_pembeli_per_menu, $r->harga_total_per_menu, $r->harga_total_ppn, $r->harga_pembeli_total, $r->keuntungan_toko, $r->keuntungan_rs, $r->keuntungan_rs_pajak,
        ]));
    }

    private function filters(Request $request): array
    {
        $start = (string) $request->query('start_date', '');
        $end = (string) $request->query('end_date', '');
        $kios = (string) $request->query('kios_filter', '');

        return [$start, $end, $kios];
    }

    private function baseOrderQuery(string $start, string $end, string $kios)
    {
        $query = KantinOrder::query()
            ->leftJoin('tb_bayar', 'tb_bayar.id_bayar', '=', 'tb_order.id_order')
            ->select([
                'tb_order.id_order',
                'tb_order.pelanggan',
                'tb_order.meja',
                'tb_order.waktu_order',
                'tb_order.nama_kios',
                'tb_bayar.id_bayar',
                DB::raw('COALESCE(tb_bayar.nominal_toko,0) as nominal_toko'),
                DB::raw('COALESCE(tb_bayar.nominal_rs,0) as nominal_rs'),
                DB::raw('COALESCE(tb_bayar.jumlah_bayar,0) as jumlah_bayar'),
                DB::raw('COALESCE(tb_bayar.diskon,0) as diskon'),
            ])
            ->orderByDesc('tb_order.waktu_order');

        if ($start) {
            $query->where('tb_order.waktu_order', '>=', $start . ' 00:00:00');
        }
        if ($end) {
            $query->where('tb_order.waktu_order', '<=', $end . ' 23:59:59');
        }
        if ($kios && $kios !== 'all') {
            $query->where('tb_order.nama_kios', $kios);
        }

        return $query;
    }

    private function csvResponse(string $filename, array $header, Collection $rows)
    {
        return response()->streamDownload(function () use ($header, $rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function applyOrderFilter($query, string $start, string $end, string $kios): void
    {
        if ($start) {
            $query->where('tb_order.waktu_order', '>=', $start . ' 00:00:00');
        }
        if ($end) {
            $query->where('tb_order.waktu_order', '<=', $end . ' 23:59:59');
        }
        if ($kios && $kios !== 'all') {
            $query->where('tb_order.nama_kios', $kios);
        }
    }
}
