<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include "../Database/connect.php";
require '../vendor/autoload.php';
$start_date_with_time = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date_with_time = isset($_POST['end_date']) ? $_POST['end_date'] : null;
$kios_filter = isset($_POST['kios_filter']) ? $_POST['kios_filter'] : null;



// Query data dari database (samakan dengan laporan.php)
if (!empty($start_date_with_time) && !empty($end_date_with_time) && $kios_filter == 'all') {
    $whereClause = "WHERE tb_order.waktu_order BETWEEN '" . $conn->real_escape_string($start_date_with_time) . "' AND '" . $conn->real_escape_string($end_date_with_time) . "'";
    $sql = "SELECT *, SUM(harga*jumlah) AS harganya, SUM(1000*jumlah) AS keuntungan_rs FROM tb_order
            LEFT JOIN user ON user.id = tb_order.kasir
            LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
            LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
            LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
            $whereClause
            GROUP BY tb_order.id_order ORDER BY tb_order.nama_kios DESC";
} else if (!empty($start_date_with_time) && !empty($end_date_with_time) && !empty($kios_filter != 'all')) {
    $whereClause = "WHERE tb_order.waktu_order BETWEEN '" . $conn->real_escape_string($start_date_with_time) . "' AND '" . $conn->real_escape_string($end_date_with_time) . "' AND tb_order.nama_kios = '" . $conn->real_escape_string($kios_filter) . "'";
    $sql = "SELECT *, SUM(harga*jumlah) AS harganya, SUM(1000*jumlah) AS keuntungan_rs FROM tb_order
            LEFT JOIN user ON user.id = tb_order.kasir
            LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
            LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
            LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
            $whereClause
            GROUP BY tb_order.id_order ORDER BY tb_order.nama_kios DESC";
} else if (!empty($kios_filter)) {
    $whereClause = "WHERE tb_order.nama_kios = '" . $conn->real_escape_string($kios_filter) . "'";
    $sql = "SELECT *, SUM(harga*jumlah) AS harganya, SUM(1000*jumlah) AS keuntungan_rs FROM tb_order
            LEFT JOIN user ON user.id = tb_order.kasir
            LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
            LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
            LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
            $whereClause
            GROUP BY tb_order.id_order ORDER BY tb_order.nama_kios DESC";
} else if ($kios_filter == 'all' && empty($start_date_with_time) && empty($end_date_with_time)) {
    $sql = "SELECT *, SUM(harga*jumlah) AS harganya, SUM(1000*jumlah) AS keuntungan_rs FROM tb_order
            LEFT JOIN user ON user.id = tb_order.kasir
            LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
            LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
            LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
            GROUP BY tb_order.id_order ORDER BY tb_order.nama_kios DESC";
} else {
    $sql = "SELECT *, SUM(harga*jumlah) AS harganya, SUM(1000*jumlah) AS keuntungan_rs FROM tb_order
                                    LEFT JOIN user ON user.id = tb_order.kasir
                                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                                    GROUP BY tb_order.id_order ORDER BY tb_order.nama_kios DESC";
}




$result = $conn->query($sql);
// var_dump($start_date_with_time);
// var_dump($end_date_with_time);
// var_dump($kios_filter);
// var_dump($result);
// exit();


// Hitung total penjualan
$total = 0;
$data = [];
while ($row = $result->fetch_assoc()) {
    $total += $row['nominal_toko'];
    $data[] = $row;
}

// --- Bagian Styling dan Excel Generation (Hanya Header & Data yang Diulang) ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Penjualan Toko');

// Tentukan Nama Toko untuk Judul Laporan dan Nama File
$nama_toko_judul = ($kios_filter && $kios_filter != 'all') ? strtoupper($kios_filter) : 'SEMUA TOKO';

// 1. JUDUL LAPORAN (Baris 1 & 2)
// ... (Bagian styling dan penulisan Judul di Baris 1 & 2 tetap sama)

$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', 'LAPORAN PENJUALAN TOKO');
$sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A2', 'TOKO: ' . $nama_toko_judul);
$sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Tentukan baris awal untuk Header Tabel
$header_row = 3; 
$data_start_row = 4;

// 2. HEADER TABEL (Baris 3)
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12,
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
// Styling untuk baris 3 (Header)
$sheet->getStyle('A' . $header_row . ':H' . $header_row)->applyFromArray($headerStyle);

// Tulis Header di baris 3
$sheet->setCellValue('A' . $header_row, 'No');
$sheet->setCellValue('B' . $header_row, 'Kode Order');
$sheet->setCellValue('C' . $header_row, 'Pelanggan');
$sheet->setCellValue('D' . $header_row, 'Meja');
$sheet->setCellValue('E' . $header_row, 'Keuntungan Toko');
$sheet->setCellValue('F' . $header_row, 'Diskon');
$sheet->setCellValue('G' . $header_row, 'Waktu Order');
$sheet->setCellValue('H' . $header_row, 'Nama Toko');

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
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

foreach ($data as $row) {
    // Terapkan warna selang-seling (Zebra Stripes)
    if ($rowNum % 2 == 0) {
        $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
    }

    $sheet->setCellValue('A' . $rowNum, $id_nomor++);
    $sheet->setCellValue('B' . $rowNum, $row['id_order']);
    $sheet->setCellValue('C' . $rowNum, $row['pelanggan']);
    $sheet->setCellValue('D' . $rowNum, $row['meja']);
    $sheet->setCellValue('E' . $rowNum, $row['nominal_toko']);
    $sheet->setCellValue('F' . $rowNum, $row['diskon']);
    $sheet->setCellValue('G' . $rowNum, $row['waktu_order']);
    $sheet->setCellValue('H' . $rowNum, $row['nama_kios']);
    
    // Terapkan border dan alignment untuk baris data
    $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray($dataStyle);

    // Format mata uang (Kolom E dan F)
    $sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0.00');
    $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0.00');
    
    // Rata kiri untuk teks (Kolom C dan H)
    $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $rowNum++;
}

// 4. TOTAL ROW (Baris terakhir)
$totalRowStyle = [
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
        'bottom' => ['borderStyle' => Border::BORDER_THICK],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];
$sheet->getStyle('D' . $rowNum . ':H' . $rowNum)->applyFromArray($totalRowStyle);

// Merge kolom untuk teks 'Total' dan Rata Kanan
$sheet->mergeCells('C' . $rowNum . ':D' . $rowNum);
$sheet->setCellValue('C' . $rowNum, 'TOTAL KEUNTUNGAN TOKO');
$sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Total Penjualan
$sheet->setCellValue('E' . $rowNum, $total);
$sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0.00');

// 5. Set Lebar Kolom Otomatis
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}


// --- MODIFIKASI UNTUK NAMA FILE DINAMIS ---
// 1. Ambil Tanggal Hari Ini
$tanggal_hari_ini = date('Y-m-d'); 

// 2. Ambil Nama Toko (digunakan yang sudah diolah untuk Judul, tapi dibersihkan dari spasi/karakter khusus)
// Mengganti spasi atau karakter non-alfanumerik dengan underscore atau strip untuk nama file yang aman.
$nama_toko_file = str_replace([' ', '/', '\\'], '-', $nama_toko_judul); 

// 3. Buat Nama File Akhir
$filename = "laporan-penjualan-" . strtolower($nama_toko_file) . "-" . $tanggal_hari_ini . ".xlsx";


// Set header untuk download file excel
if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"'); // Menggunakan $filename
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>