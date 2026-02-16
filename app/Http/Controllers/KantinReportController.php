<?php

namespace App\Http\Controllers;

use App\Models\KantinKios;
use App\Models\KantinOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KantinReportController extends Controller
{
    // Daftar menu khusus untuk laporan rekap RS (sesuai kebutuhan bisnis lama).
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
        // Laporan detail transaksi lengkap nominal toko + RS.
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
        // Laporan fokus pendapatan RS.
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
        // Laporan fokus pendapatan toko/kios.
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
        // Rekap penjualan per menu.
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
        // Rekap kontribusi pajak/RS per menu.
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
        // Rekap RS khusus subset menu minuman tertentu.
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
        // Breakdown keuangan detail per transaksi-item.
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
        // Breakdown keuangan terakumulasi per menu.
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
        $filename = $this->buildExportFilename('laporan_detail', $start, $end, $kios);
        $header = [
            'kode_order', 'pelanggan', 'meja', 'nominal_toko', 'nominal_rs', 'jumlah_bayar', 'diskon', 'waktu_order', 'nama_kios',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->id_order, $r->pelanggan, $r->meja, $r->nominal_toko, $r->nominal_rs, $r->jumlah_bayar, $r->diskon, $r->waktu_order, $r->nama_kios,
        ]);
        $title = 'Tarikan Data Pendapatan Detail';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, $kios);
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, $kios);
    }

    public function exportRsCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();
        $filename = $this->buildExportFilename('laporan_rs', $start, $end, $kios);
        $header = [
            'kode_order', 'pelanggan', 'meja', 'nominal_rs', 'waktu_order', 'nama_kios',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->id_order, $r->pelanggan, $r->meja, $r->nominal_rs, $r->waktu_order, $r->nama_kios,
        ]);
        $title = 'Tarikan Data Pendapatan RS';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, $kios);
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, $kios);
    }

    public function exportTokoCsv(Request $request)
    {
        [$start, $end, $kios] = $this->filters($request);
        $rows = $this->baseOrderQuery($start, $end, $kios)->get();
        $filename = $this->buildExportFilename('laporan_toko', $start, $end, $kios);
        $header = [
            'kode_order', 'pelanggan', 'meja', 'nominal_toko', 'diskon', 'waktu_order', 'nama_kios',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->id_order, $r->pelanggan, $r->meja, $r->nominal_toko, $r->diskon, $r->waktu_order, $r->nama_kios,
        ]);
        $title = 'Tarikan Data Pendapatan Toko';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, $kios);
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, $kios);
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
        $filename = $this->buildExportFilename('laporan_menu', $start, $end, $kios);
        $header = [
            'nama_menu', 'nama_toko', 'total_terjual', 'harga_satuan', 'total_harga',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->nama_menu, $r->nama_toko, $r->total_terjual, $r->harga_satuan, $r->total_harga,
        ]);
        $title = 'Tarikan Data Rekap Penjualan Menu';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, $kios);
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, $kios);
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
        $filename = $this->buildExportFilename('laporan_rekap_rs', $start, $end, $kios);
        $header = [
            'nama_menu', 'nama_toko', 'total_terjual', 'harga_satuan', 'total_harga',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->nama, $r->nama_toko, $r->total_terjual, $r->harga_satuan, $r->total_harga,
        ]);
        $title = 'Tarikan Data Rekap Kontribusi RS';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, $kios);
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, $kios);
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
        $filename = $this->buildExportFilename('laporan_rekap_menu_rs', $start, $end, 'all');
        $header = [
            'nama_menu', 'total_terjual', 'harga_satuan', 'total_harga',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->nama, $r->total_terjual, $r->harga_satuan, $r->total_harga,
        ]);
        $title = 'Tarikan Data Rekap Menu Target RS';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, 'all');
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, 'all');
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
        $filename = $this->buildExportFilename('laporan_finance_detail', $start, $end, $kios);
        $header = [
            'waktu_order', 'nama_menu', 'jumlah', 'nama_toko', 'harga_jual', 'ppn', 'harga_pembeli', 'total_menu', 'total_ppn', 'total_pembeli', 'untung_toko', 'untung_rs', 'untung_rs_pajak',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->waktu_order, $r->nama_menu, $r->jumlah_terjual, $r->nama_toko, $r->harga_jual_per_menu, $r->harga_ppn, $r->harga_pembeli_per_menu, $r->harga_total_per_menu, $r->harga_total_ppn, $r->harga_pembeli_total, $r->keuntungan_toko, $r->keuntungan_rs, $r->keuntungan_rs_pajak,
        ]);
        $title = 'Tarikan Data Rekap Keuangan Detail';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, $kios);
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, $kios);
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
        $filename = $this->buildExportFilename('laporan_finance_menu', $start, $end, $kios);
        $header = [
            'nama_menu', 'jumlah', 'nama_toko', 'harga_jual', 'ppn', 'harga_pembeli', 'total_menu', 'total_ppn', 'total_pembeli', 'untung_toko', 'untung_rs', 'untung_rs_pajak',
        ];
        $mappedRows = $rows->map(fn ($r) => [
            $r->nama_menu, $r->jumlah_terjual, $r->nama_toko, $r->harga_jual_per_menu, $r->harga_ppn, $r->harga_pembeli_per_menu, $r->harga_total_per_menu, $r->harga_total_ppn, $r->harga_pembeli_total, $r->keuntungan_toko, $r->keuntungan_rs, $r->keuntungan_rs_pajak,
        ]);
        $title = 'Tarikan Data Rekap Keuangan per Menu';

        if ((string) $request->query('format') === 'pdf') {
            return $this->pdfResponse(str_replace('.xlsx', '.pdf', $filename), $header, $mappedRows, $title, $start, $end, $kios);
        }

        return $this->xlsxResponse($filename, $header, $mappedRows, $title, $start, $end, $kios);
    }

    private function filters(Request $request): array
    {
        // Ambil filter tanggal dan kios dari query string.
        $start = (string) $request->query('start_date', '');
        $end = (string) $request->query('end_date', '');
        $kios = (string) $request->query('kios_filter', '');

        return [$start, $end, $kios];
    }

    private function baseOrderQuery(string $start, string $end, string $kios)
    {
        // Query dasar laporan order agar dipakai ulang di banyak endpoint.
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

    private function xlsxResponse(string $filename, array $header, Collection $rows, string $reportTitle, string $start, string $end, string $kios)
    {
        // Export ke format .xlsx dengan styling agar langsung rapi saat dibuka di Excel.
        return response()->streamDownload(function () use ($header, $rows, $reportTitle, $start, $end, $kios): void {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan');

            $periodLabel = $this->buildPeriodLabel($start, $end);
            $kiosLabel = $this->buildKiosLabel($kios);
            $generatedAt = now()->format('d-m-Y H:i:s');

            $sheet->setCellValue('A1', 'SAKINA KANTIN - EXPORT DATA');
            $sheet->setCellValue('A2', $reportTitle);
            $sheet->setCellValue('A3', 'Periode: ' . $periodLabel);
            $sheet->setCellValue('A4', 'Filter Toko: ' . $kiosLabel);
            $sheet->setCellValue('A5', 'Waktu Tarik: ' . $generatedAt);

            $headerRow = 7;
            $dataStartRow = $headerRow + 1;
            [$moneyColumnIndexes, $countColumnIndexes] = $this->resolveNumericColumnTypes($header);

            $sheet->fromArray($header, null, "A{$headerRow}");
            $rowData = $rows->map(function ($row) use ($moneyColumnIndexes, $countColumnIndexes) {
                $values = array_values(is_array($row) ? $row : (array) $row);
                foreach ($moneyColumnIndexes as $idx) {
                    if (array_key_exists($idx, $values) && $values[$idx] !== null && $values[$idx] !== '') {
                        $numericValue = str_replace(',', '', (string) $values[$idx]);
                        $values[$idx] = is_numeric($numericValue) ? (float) $numericValue : $values[$idx];
                    }
                }
                foreach ($countColumnIndexes as $idx) {
                    if (array_key_exists($idx, $values) && $values[$idx] !== null && $values[$idx] !== '') {
                        $numericValue = str_replace(',', '', (string) $values[$idx]);
                        $values[$idx] = is_numeric($numericValue) ? (int) round((float) $numericValue) : $values[$idx];
                    }
                }

                return $values;
            })->values()->all();
            if (!empty($rowData)) {
                $sheet->fromArray($rowData, null, "A{$dataStartRow}");
            }

            $highestColumn = Coordinate::stringFromColumnIndex(count($header));
            $dataLastRow = !empty($rowData) ? ($dataStartRow + count($rowData) - 1) : ($dataStartRow - 1);
            $totalRow = $dataLastRow >= $dataStartRow ? $dataLastRow + 1 : $dataStartRow;
            $lastRow = max($headerRow, $totalRow);
            $fullRange = "A{$headerRow}:{$highestColumn}{$lastRow}";
            $headerRange = "A{$headerRow}:{$highestColumn}{$headerRow}";
            $dataRange = $dataLastRow >= $dataStartRow ? "A{$dataStartRow}:{$highestColumn}{$dataLastRow}" : null;
            $titleRange = "A1:{$highestColumn}1";
            $subTitleRange = "A2:{$highestColumn}2";
            $metaRange = "A3:{$highestColumn}5";

            $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
            $sheet->mergeCells($titleRange);
            $sheet->mergeCells($subTitleRange);
            $sheet->mergeCells("A3:{$highestColumn}3");
            $sheet->mergeCells("A4:{$highestColumn}4");
            $sheet->mergeCells("A5:{$highestColumn}5");
            $sheet->freezePane("A{$dataStartRow}");
            $sheet->setAutoFilter($headerRange);

            $sheet->getStyle($titleRange)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0B5394'],
                ],
            ]);

            $sheet->getStyle($subTitleRange)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F2937']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2'],
                ],
            ]);

            $sheet->getStyle($metaRange)->applyFromArray([
                'font' => ['bold' => false, 'size' => 10, 'color' => ['rgb' => '374151']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F4E78'],
                ],
            ]);

            $sheet->getStyle($fullRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9D9D9'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            if ($dataRange) {
                $sheet->getStyle($dataRange)->getAlignment()->setWrapText(true);
                for ($r = $dataStartRow; $r <= $dataLastRow; $r++) {
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                    }
                }
            }

            $sheet->setCellValue("A{$totalRow}", 'JUMLAH');
            $sheet->getStyle("A{$totalRow}:{$highestColumn}{$totalRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC'],
                ],
            ]);

            foreach ($header as $idx => $name) {
                $column = Coordinate::stringFromColumnIndex($idx + 1);
                $headerName = strtolower((string) $name);
                $isMoney = in_array($idx, $moneyColumnIndexes, true);
                $isCount = in_array($idx, $countColumnIndexes, true);

                if ($dataLastRow >= $dataStartRow && ($isMoney || $isCount)) {
                    $sheet->setCellValue("{$column}{$totalRow}", "=SUM({$column}{$dataStartRow}:{$column}{$dataLastRow})");
                } elseif ($isMoney || $isCount) {
                    $sheet->setCellValue("{$column}{$totalRow}", 0);
                }

                if ($lastRow >= $dataStartRow && $isMoney) {
                    $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode('"Rp" #,##0.00;[Red]-"Rp" #,##0.00');
                    $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } elseif ($lastRow >= $dataStartRow && $isCount) {
                    $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                    $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } elseif (!$isMoney && !$isCount && $headerName !== 'kode_order') {
                    $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function pdfResponse(string $filename, array $header, Collection $rows, string $reportTitle, string $start, string $end, string $kios)
    {
        [$moneyColumnIndexes, $countColumnIndexes] = $this->resolveNumericColumnTypes($header);
        $normalizedRows = $rows->map(function ($row) use ($moneyColumnIndexes, $countColumnIndexes) {
            $values = array_values(is_array($row) ? $row : (array) $row);
            foreach ($moneyColumnIndexes as $idx) {
                if (array_key_exists($idx, $values) && $values[$idx] !== null && $values[$idx] !== '') {
                    $numericValue = str_replace(',', '', (string) $values[$idx]);
                    $values[$idx] = is_numeric($numericValue) ? (float) $numericValue : $values[$idx];
                }
            }
            foreach ($countColumnIndexes as $idx) {
                if (array_key_exists($idx, $values) && $values[$idx] !== null && $values[$idx] !== '') {
                    $numericValue = str_replace(',', '', (string) $values[$idx]);
                    $values[$idx] = is_numeric($numericValue) ? (int) round((float) $numericValue) : $values[$idx];
                }
            }

            return $values;
        })->values()->all();

        $totals = array_fill(0, count($header), null);
        foreach ($moneyColumnIndexes as $idx) {
            $totals[$idx] = (float) collect($normalizedRows)->sum(fn ($r) => (float) ($r[$idx] ?? 0));
        }
        foreach ($countColumnIndexes as $idx) {
            $totals[$idx] = (int) collect($normalizedRows)->sum(fn ($r) => (int) ($r[$idx] ?? 0));
        }

        $pdf = Pdf::loadView('app.report.export_pdf', [
            'reportTitle' => $reportTitle,
            'periodLabel' => $this->buildPeriodLabel($start, $end),
            'kiosLabel' => $this->buildKiosLabel($kios),
            'generatedAt' => now()->format('d-m-Y H:i:s'),
            'header' => $header,
            'rows' => $normalizedRows,
            'totals' => $totals,
            'moneyColumnIndexes' => $moneyColumnIndexes,
            'countColumnIndexes' => $countColumnIndexes,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function resolveNumericColumnTypes(array $header): array
    {
        $moneyKeywords = ['nominal', 'harga', 'total', 'untung', 'diskon', 'ppn', 'bayar'];
        $countKeywords = ['jumlah', 'terjual'];
        $moneyColumnIndexes = [];
        $countColumnIndexes = [];

        foreach ($header as $idx => $name) {
            $headerName = strtolower((string) $name);
            foreach ($countKeywords as $keyword) {
                if (str_contains($headerName, $keyword)) {
                    $countColumnIndexes[] = $idx;
                    continue 2;
                }
            }
            foreach ($moneyKeywords as $keyword) {
                if (str_contains($headerName, $keyword)) {
                    $moneyColumnIndexes[] = $idx;
                    continue 2;
                }
            }
        }

        return [$moneyColumnIndexes, $countColumnIndexes];
    }

    private function buildExportFilename(string $reportKey, string $start, string $end, string $kios): string
    {
        $periodPart = $this->buildPeriodFilenamePart($start, $end);
        $kiosPart = $this->sanitizeFilenamePart($this->buildKiosLabel($kios));

        return "{$reportKey}_{$periodPart}_{$kiosPart}.xlsx";
    }

    private function buildPeriodFilenamePart(string $start, string $end): string
    {
        if ($start && $end) {
            return "{$start}_sd_{$end}";
        }
        if ($start) {
            return "dari_{$start}";
        }
        if ($end) {
            return "sampai_{$end}";
        }

        return 'semua_tanggal';
    }

    private function buildPeriodLabel(string $start, string $end): string
    {
        if ($start && $end) {
            return "{$start} s/d {$end}";
        }
        if ($start) {
            return "Mulai {$start}";
        }
        if ($end) {
            return "Sampai {$end}";
        }

        return 'Semua Tanggal';
    }

    private function buildKiosLabel(string $kios): string
    {
        if (!$kios || $kios === 'all') {
            return 'Semua Toko';
        }

        return trim($kios);
    }

    private function sanitizeFilenamePart(string $value): string
    {
        $value = Str::ascii($value);
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value) ?? 'data';
        $value = trim($value, '_');

        return $value !== '' ? $value : 'data';
    }

    private function applyOrderFilter($query, string $start, string $end, string $kios): void
    {
        // Terapkan filter yang sama ke semua query laporan.
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
