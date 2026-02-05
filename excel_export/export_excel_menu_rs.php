<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include "../Database/connect.php"; // Pastikan path ke connect.php sudah benar
require '../vendor/autoload.php';

// --- 1. AMBIL DAN SANITASI DATA FILTER DARI POST ---
$start_date = isset($_POST['start_date']) ? mysqli_real_escape_string($conn, $_POST['start_date']) : '';
$end_date = isset($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : '';
$kios_filter = isset($_POST['kios_filter']) ? mysqli_real_escape_string($conn, $_POST['kios_filter']) : '';

$where_clause = "";
$where_parts = [];
$filter_info = "SEMUA WAKTU dan SEMUA TOKO"; // Default info

// Logika Filter Kios
if (!empty($kios_filter) && $kios_filter != 'all') {
    $where_parts[] = "tb_order.nama_kios = '$kios_filter'";
    $filter_info = "TOKO: " . $kios_filter;
} elseif ($kios_filter == 'all') {
    $filter_info = "SEMUA TOKO";
}

// Logika Filter Tanggal
$tanggal_info = "";
if (!empty($start_date) && !empty($end_date)) {
    $start_date_with_time = $start_date . " 00:00:00";
    $end_date_with_time = $end_date . " 23:59:59";
    $where_parts[] = "tb_order.waktu_order BETWEEN '$start_date_with_time' AND '$end_date_with_time'";
    $tanggal_info = " Tgl. " . date('d/m/Y', strtotime($start_date)) . " s/d " . date('d/m/Y', strtotime($end_date));
} else if (!empty($start_date)) {
    $start_date_with_time = $start_date . " 00:00:00";
    $where_parts[] = "tb_order.waktu_order >= '$start_date_with_time'";
    $tanggal_info = " Mulai Tgl. " . date('d/m/Y', strtotime($start_date));
} else if (!empty($end_date)) {
    $end_date_with_time = $end_date . " 23:59:59";
    $where_parts[] = "tb_order.waktu_order <= '$end_date_with_time'";
    $tanggal_info = " Sampai Tgl. " . date('d/m/Y', strtotime($end_date));
}

if (!empty($where_parts)) {
    $where_clause = " WHERE " . implode(" AND ", $where_parts);
    // Update info jika ada filter
    $filter_info = trim($filter_info . $tanggal_info);
}


// --- 2. PENGAMBILAN DATA DENGAN KLAUSA WHERE DINAMIS ---
// Query data dari database 
$sql = "SELECT tb_menu.nama, sum(tb_list_order.jumlah) AS Total_Terjual, tb_menu.pajak AS harga_satuan, SUM(tb_list_order.jumlah)*tb_menu.pajak as Total_harga, tb_menu.nama_toko 
        FROM tb_order
        LEFT JOIN user ON user.id = tb_order.kasir
        LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
        LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
        LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
        $where_clause 
        GROUP BY tb_menu.nama, tb_menu.nama_toko, tb_menu.harga 
        ORDER BY Total_Terjual DESC, tb_menu.nama_toko ASC"; // Diurutkan berdasarkan Total_Terjual DESC dan Nama Toko

$result = $conn->query($sql);
if (!$result) {
    die("Query Error: " . $conn->error);
}

$data = [];
$grand_total_jual = 0; 
while ($row = $result->fetch_assoc()) {
    $grand_total_jual += $row['Total_harga'];
    $data[] = $row;
}
// --- Akhir Bagian Pengambilan Data ---


// --- 3. EXCEL GENERATION ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Penjualan Per Menu');

// Tentukan Nama Kolom Terakhir (F)
$last_col = 'F';

// 1. JUDUL LAPORAN (Baris 1 & 2)
$sheet->mergeCells('A1:' . $last_col . '1');
$sheet->setCellValue('A1', 'LAPORAN REKAPITULASI KEUNTUNGAN PER MENU');
$sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:' . $last_col . '2');
// Menampilkan info filter yang diterapkan
$sheet->setCellValue('A2', strtoupper($filter_info) . ' (DIURUTKAN BERDASARKAN TOTAL TERJUAL)');
$sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Tentukan baris awal untuk Header Tabel
$header_row = 3; 
$data_start_row = 4;

// 2. HEADER TABEL (Baris 3)
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'], 
        'size' => 11,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '0070C0'], 
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A' . $header_row . ':' . $last_col . $header_row)->applyFromArray($headerStyle);

// Tulis Header di baris 3 (Disesuaikan dengan kolom di laporan sebelumnya: tanpa Waktu Order)
$sheet->setCellValue('A' . $header_row, 'No');
$sheet->setCellValue('B' . $header_row, 'Nama Menu');
$sheet->setCellValue('C' . $header_row, 'Nama Toko');
$sheet->setCellValue('D' . $header_row, 'Total Item Terjual');
$sheet->setCellValue('E' . $header_row, 'Harga Satuan');
$sheet->setCellValue('F' . $header_row, 'Total Harga Jual');

// 3. DATA ROWS (Mulai Baris 4)
$rowNum = $data_start_row;
$id_nomor = 1;
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'DDDDDD'],
        ],
    ],
];

foreach ($data as $row) {
    // Terapkan warna selang-seling (Zebra Stripes)
    if ($rowNum % 2 == 0) {
        $sheet->getStyle('A' . $rowNum . ':' . $last_col . $rowNum)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
    }

    $sheet->setCellValue('A' . $rowNum, $id_nomor++);
    $sheet->setCellValue('B' . $rowNum, $row['nama']);
    $sheet->setCellValue('C' . $rowNum, $row['nama_toko']);
    $sheet->setCellValue('D' . $rowNum, $row['Total_Terjual']);
    $sheet->setCellValue('E' . $rowNum, $row['harga_satuan']);
    $sheet->setCellValue('F' . $rowNum, $row['Total_harga']);
    
    // Terapkan border dan alignment untuk baris data
    $sheet->getStyle('A' . $rowNum . ':' . $last_col . $rowNum)->applyFromArray($dataStyle);

    // Format angka/teks khusus
    $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
    $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Nama Menu
    $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Nama Toko
    $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Total Terjual
    $sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0'); // Harga Satuan
    $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0'); // Total Harga Jual

    $rowNum++;
}

// 4. TOTAL ROW (Baris terakhir)
$totalRowStyle = [
    'font' => [
        'bold' => true,
        'size' => 12,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFF2CC'], 
    ],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THICK],
        'bottom' => ['borderStyle' => Border::BORDER_THICK],
    ],
];
$sheet->getStyle('A' . $rowNum . ':' . $last_col . $rowNum)->applyFromArray($totalRowStyle);

// Gabungkan kolom A sampai E untuk teks 'Total' (Total 5 kolom digabung)
$sheet->mergeCells('A' . $rowNum . ':E' . $rowNum);
$sheet->setCellValue('A' . $rowNum, 'GRAND TOTAL PENJUALAN');
$sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Nilai Grand Total
$sheet->setCellValue('F' . $rowNum, $grand_total_jual);
$sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0'); 

// 5. Set Lebar Kolom Otomatis
foreach (range('A', $last_col) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}


// --- NAMA FILE DINAMIS ---
$tanggal_hari_ini = date('Y-m-d'); 
$filename = "laporan-penjualan-menu-terlaris-" . $tanggal_hari_ini . ".xlsx";


// Set header untuk download file excel
if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>