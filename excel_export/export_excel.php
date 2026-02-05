<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include "../Database/connect.php";
require '../vendor/autoload.php';

// Inisialisasi variabel filter dari POST
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$kios_filter = isset($_POST['kios_filter']) ? $_POST['kios_filter'] : '';
$where_clause = "";

// 1. Sanitasi input filter
$start_date_esc = $conn->real_escape_string($start_date);
$end_date_esc = $conn->real_escape_string($end_date);
$kios_filter_esc = $conn->real_escape_string($kios_filter);

// Logika penentuan klausa WHERE (Disamakan dengan laporan.php)
$where_parts = [];

// 1. Filter Kios
if (!empty($kios_filter_esc) && $kios_filter_esc != 'all') {
    $where_parts[] = "tb_order.nama_kios = '$kios_filter_esc'";
}

// 2. Filter Tanggal
if (!empty($start_date_esc) && !empty($end_date_esc)) {
    $start_date_with_time = $start_date_esc . " 00:00:00";
    $end_date_with_time = $end_date_esc . " 23:59:59";
    $where_parts[] = "tb_order.waktu_order BETWEEN '$start_date_with_time' AND '$end_date_with_time'";
} else if (!empty($start_date_esc)) {
    $start_date_with_time = $start_date_esc . " 00:00:00";
    $where_parts[] = "tb_order.waktu_order >= '$start_date_with_time'";
} else if (!empty($end_date_esc)) {
    $end_date_with_time = $end_date_esc . " 23:59:59";
    $where_parts[] = "tb_order.waktu_order <= '$end_date_with_time'";
}

if (!empty($where_parts)) {
    $where_clause = " WHERE " . implode(" AND ", $where_parts);
}

// 2. Query Data dari Database (Disamakan dengan laporan.php)
// Catatan: Query di laporan.php tidak memerlukan join ke tabel 'user' karena tidak menampilkan nama kasir.
// Jika Anda ingin nama kasir, kolom 'kasir' di tb_order harus berisi ID user.
$query_string = "SELECT 
                        tb_order.*, 
                        tb_bayar.id_bayar, tb_bayar.jumlah_bayar, tb_bayar.diskon, tb_bayar.nominal_toko, tb_bayar.nominal_rs,
                        -- Join ke user hanya untuk mendapatkan nama kasir (username). Asumsikan tb_order.kasir = user.id
                        user.username 
                    FROM tb_order
                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
                    LEFT JOIN user ON user.id = tb_order.kasir
                    $where_clause
                    GROUP BY tb_order.id_order 
                    ORDER BY tb_order.waktu_order DESC";


$result = $conn->query($query_string);
if (!$result) {
    die("Query Error: " . $conn->error);
}

// Hitung total penjualan
$total_toko = 0;
$total_rs = 0;
$total_diskon = 0;
$total_bayar = 0;
$data = [];
while ($row = $result->fetch_assoc()) {
    $total_toko += $row['nominal_toko'] ?? 0;
    $total_rs += $row['nominal_rs'] ?? 0;
    $total_diskon += $row['diskon'] ?? 0;
    $total_bayar += $row['jumlah_bayar'] ?? 0; // Total Bayar Final (setelah diskon)
    $data[] = $row;
}

// --- Bagian Styling dan Excel Generation ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Pembayaran Detail');

// Tentukan Nama Kolom Terakhir (L)
$last_col = 'L';

// Tentukan Nama Toko untuk Judul Laporan dan Nama File
$nama_toko_judul = ($kios_filter && $kios_filter != 'all') ? strtoupper($kios_filter) : 'SEMUA TOKO';

// 1. JUDUL LAPORAN (Baris 1 & 2)
$sheet->mergeCells('A1:' . $last_col . '1');
$sheet->setCellValue('A1', 'LAPORAN DETAIL PEMBAYARAN');
$sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:' . $last_col . '2');
// Tambahkan informasi rentang tanggal pada judul jika ada filter tanggal
$tanggal_info_judul = "";
if (!empty($start_date) && !empty($end_date)) {
    $tanggal_info_judul = " | TANGGAL: " . $start_date . " s/d " . $end_date;
} else if (!empty($start_date)) {
    $tanggal_info_judul = " | MULAI TANGGAL: " . $start_date;
} else if (!empty($end_date)) {
    $tanggal_info_judul = " | SAMPAI TANGGAL: " . $end_date;
}
$sheet->setCellValue('A2', 'TOKO: ' . $nama_toko_judul . $tanggal_info_judul);
$sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Tentukan baris awal untuk Header Tabel
$header_row = 4; // Dimulai di baris 4 karena baris 1-3 untuk Judul/Info
$data_start_row = 5;

// 2. HEADER TABEL (Baris 4)
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'], // Teks Putih
        'size' => 12,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '0070C0'], // Background Biru
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
// Styling untuk baris 4 (Header)
$sheet->getStyle('A' . $header_row . ':' . $last_col . $header_row)->applyFromArray($headerStyle);

// Tulis Header di baris 4 (Disamakan dengan kolom di laporan.php)
$sheet->setCellValue('A' . $header_row, 'No');
$sheet->setCellValue('B' . $header_row, 'Kode Order');
$sheet->setCellValue('C' . $header_row, 'Pelanggan');
$sheet->setCellValue('D' . $header_row, 'Meja');
$sheet->setCellValue('E' . $header_row, 'Pendapatan Toko');
$sheet->setCellValue('F' . $header_row, 'Pendapatan Sakina Food Court');
$sheet->setCellValue('G' . $header_row, 'Total Bayar');
$sheet->setCellValue('H' . $header_row, 'Status'); // Kolom Status
$sheet->setCellValue('I' . $header_row, 'Diskon');
$sheet->setCellValue('J' . $header_row, 'Waktu Order');
$sheet->setCellValue('K' . $header_row, 'Nama Toko');
$sheet->setCellValue('L' . $header_row, 'Kasir'); // Tambahkan kolom Kasir (meski di laporan.php tidak ada, ini data penting)

// 3. DATA ROWS (Mulai Baris 5)
$rowNum = $data_start_row;
$id_nomor = 1;
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'DDDDDD'],
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

foreach ($data as $row) {
    // Terapkan warna selang-seling (Zebra Stripes)
    if ($rowNum % 2 != 0) { // Ganti ke ganjil/odd untuk menyesuaikan hitungan baris Excel
        $sheet->getStyle('A' . $rowNum . ':' . $last_col . $rowNum)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
    }
    
    $status = !empty($row['id_bayar']) ? 'Dibayar' : 'Belum Dibayar';

    $sheet->setCellValue('A' . $rowNum, $id_nomor++);
    $sheet->setCellValue('B' . $rowNum, $row['id_order']);
    $sheet->setCellValue('C' . $rowNum, $row['pelanggan']);
    $sheet->setCellValue('D' . $rowNum, $row['meja']);
    $sheet->setCellValue('E' . $rowNum, $row['nominal_toko'] ?? 0); // Pendapatan Toko
    $sheet->setCellValue('F' . $rowNum, $row['nominal_rs'] ?? 0);   // Pendapatan Sakina Food Court
    $sheet->setCellValue('G' . $rowNum, $row['jumlah_bayar'] ?? 0); // Total Bayar
    $sheet->setCellValue('H' . $rowNum, $status);                   // Status
    $sheet->setCellValue('I' . $rowNum, $row['diskon'] ?? 0);       // Diskon
    $sheet->setCellValue('J' . $rowNum, $row['waktu_order']);      // Waktu Order
    $sheet->setCellValue('K' . $rowNum, $row['nama_kios']);        // Nama Toko
    $sheet->setCellValue('L' . $rowNum, $row['username']);         // Kasir (dari join ke user)
    
    // Terapkan border dan alignment untuk baris data
    $sheet->getStyle('A' . $rowNum . ':' . $last_col . $rowNum)->applyFromArray($dataStyle);

    // Format mata uang (Kolom E, F, G, I)
    $sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
    $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
    $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
    $sheet->getStyle('I' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
    
    // Rata kiri untuk teks (Kolom C, K, L)
    $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('K' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('L' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $rowNum++;
}

// 4. TOTAL ROW (Baris terakhir data + 1)
$grand_total = $total_toko + $total_rs + $total_diskon; // Grand Total Kotor = Toko + RS + Diskon

// Styling untuk Baris Total 1 (Ringkasan Keuntungan)
$totalRowStyle1 = [
    'font' => [
        'bold' => true,
        'size' => 13,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'DDEBF7'],
    ],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THICK],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

// Baris Ringkasan Total
$sheet->getStyle('A' . $rowNum . ':' . $last_col . $rowNum)->applyFromArray($totalRowStyle1);

// Gabungkan kolom A sampai D untuk teks 'TOTAL'
$sheet->mergeCells('A' . $rowNum . ':D' . $rowNum);
$sheet->setCellValue('A' . $rowNum, 'TOTAL');
$sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Nilai Total
$sheet->setCellValue('E' . $rowNum, $total_toko);
$sheet->setCellValue('F' . $rowNum, $total_rs);
$sheet->setCellValue('G' . $rowNum, $total_bayar);
$sheet->setCellValue('I' . $rowNum, $total_diskon);

// Format mata uang untuk Total
$sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
$sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
$sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
$sheet->getStyle('I' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
$sheet->mergeCells('H' . $rowNum . ':H' . $rowNum); // Kolom Status kosong
$sheet->mergeCells('J' . $rowNum . ':' . $last_col . $rowNum); // Kolom sisa di gabungkan

$rowNum++;

// Styling untuk Baris Total 2 (Total Pendapatan Gabungan)
$totalRowStyle2 = [
    'font' => [
        'bold' => true,
        'size' => 13,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'D0E8D0'], // Warna Hijau Muda untuk Grand Total
    ],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THICK],
        'bottom' => ['borderStyle' => Border::BORDER_THICK],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

$sheet->getStyle('A' . $rowNum . ':' . $last_col . $rowNum)->applyFromArray($totalRowStyle2);

// Gabungkan kolom A sampai D untuk teks 'Total Pendapatan'
$sheet->mergeCells('A' . $rowNum . ':D' . $rowNum);
$sheet->setCellValue('A' . $rowNum, 'GRAND TOTAL KOTOR (Toko + Food Court + Diskon)');
$sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


// Nilai Grand Total Kotor
$sheet->setCellValue('E' . $rowNum, $grand_total);
$sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0'); // Format mata uang

// Gabungkan kolom E sampai L untuk kolom Grand Total
$sheet->mergeCells('E' . $rowNum . ':' . $last_col . $rowNum); 


// 5. Set Lebar Kolom Otomatis
foreach (range('A', $last_col) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}


// --- Pengaturan Download File Excel ---
// 1. Ambil Tanggal Hari Ini
$tanggal_hari_ini = date('Y-m-d'); 

// 2. Ambil Nama Toko (dibersihkan dari spasi/karakter khusus)
$nama_toko_file = str_replace([' ', '/', '\\'], '-', $nama_toko_judul); 

// 3. Buat Nama File Akhir
$filename = "laporan-detail-pembayaran-" . strtolower($nama_toko_file) . "-" . $tanggal_hari_ini . ".xlsx";


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