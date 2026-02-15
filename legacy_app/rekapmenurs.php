<?php
include "Database/connect.php";
date_default_timezone_set("Asia/Jakarta");

// Inisialisasi variabel
$where_clause = "";
$start_date = "";
$end_date = "";

// **Daftar Menu yang Diinginkan**
$target_menus = ['Es Teh', 'Es Jeruk', 'Es Milo', 'Es Susu', 'Es Coffe Mix','Air Putih','Nutrisari','Es Good Day'];
// Membuat klausa SQL untuk memfilter menu target
$menu_filter_sql = "tb_menu.nama IN ('" . implode("', '", array_map('mysqli_real_escape_string', array_fill(0, count($target_menus), $conn), $target_menus)) . "')";

// Logika penentuan query string utama
if (isset($_POST['filter'])) {
    // Ambil input dari form dan lakukan sanitasi
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    
    // Tentukan klausa WHERE berdasarkan filter
    $where_parts = [];
    
    // 1. Filter Menu (Klausa wajib)
    $where_parts[] = $menu_filter_sql;
    
    // 2. Filter Tanggal
    if (!empty($start_date) && !empty($end_date)) {
        $start_date_with_time = $start_date . " 00:00:00";
        $end_date_with_time = $end_date . " 23:59:59";
        $where_parts[] = "tb_order.waktu_order BETWEEN '$start_date_with_time' AND '$end_date_with_time'";
    } else if (!empty($start_date) && empty($end_date)) {
        $start_date_with_time = $start_date . " 00:00:00";
        $where_parts[] = "tb_order.waktu_order >= '$start_date_with_time'";
    } else if (empty($start_date) && !empty($end_date)) {
        $end_date_with_time = $end_date . " 23:59:59";
        $where_parts[] = "tb_order.waktu_order <= '$end_date_with_time'";
    }
    
    if (!empty($where_parts)) {
        $where_clause = " WHERE " . implode(" AND ", $where_parts);
    }
    
    // Query untuk semua filter
    $query_string = "SELECT 
                        tb_menu.nama, 
                        sum(tb_list_order.jumlah) AS Total_Terjual, 
                        tb_menu.pajak AS harga_satuan, 
                        SUM(tb_list_order.jumlah)*tb_menu.pajak as Total_harga
                    FROM tb_order
                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                    $where_clause
                    GROUP BY tb_menu.nama
                    ORDER BY tb_menu.nama ASC";

} else {
    // Query default (tampilkan semua data menu target tanpa filter tanggal)
    $where_clause = " WHERE " . $menu_filter_sql;
    $query_string = "SELECT 
                        tb_menu.nama, 
                        sum(tb_list_order.jumlah) AS Total_Terjual, 
                        tb_menu.pajak AS harga_satuan, 
                        SUM(tb_list_order.jumlah)*tb_menu.pajak as Total_harga
                    FROM tb_order
                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                    $where_clause
                    GROUP BY tb_menu.nama
                    ORDER BY tb_menu.nama ASC";
}

// Jalankan query utama
$query = mysqli_query($conn, $query_string);
if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}

$result = [];
while ($record = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $result[] = $record;
}

?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-fork-knife"></i>
            Laporan
        </div>
        <div class="card-body">
            <div>
                <form method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary" name="filter" value="filter">Filter</button>
                        </div>
                        <div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col">
                    <?php
                    // Daftar menu yang difilter untuk ditampilkan di info
                    $menu_list = implode(", ", $target_menus);
                    
                    if (isset($_POST['filter'])) {
                        $info_filter = "Menampilkan data laporan untuk menu **$menu_list**";
                        
                        $tanggal_info = [];
                        if (!empty($start_date)) {
                            $tanggal_info[] = "dari **" . htmlspecialchars($start_date) . "**";
                        }
                        if (!empty($end_date)) {
                            $tanggal_info[] = "sampai **" . htmlspecialchars($end_date) . "**";
                        }
                        
                        if (!empty($tanggal_info)) {
                            $info_filter .= " " . implode(" ", $tanggal_info);
                        } else {
                            $info_filter .= " untuk **Semua Waktu**";
                        }
                        
                        echo "<p class='alert alert-info'>$info_filter</p>";
                    } else {
                        echo "<p class='alert alert-info'>Menampilkan data laporan untuk menu **$menu_list** (Semua Waktu).</p>";
                    }
                    ?>
                    
                    <table class="table table-hover" id="table_laporan">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Menu</th>
                                <th scope="col">Total Item Terjual</th>
                                <th scope="col">Harga Satuan</th>
                                <th scope="col">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $id_nomor = 1;
                            $grand_total = 0; // Inisialisasi grand total
                            foreach ($result as $row) {
                                $total_harga_menu = $row['Total_harga'] ?? 0;
                                $grand_total += $total_harga_menu;
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $id_nomor++ ?></th>
                                    <td><?php echo htmlspecialchars($row['nama'] ?? '') ?></td>
                                    <td><?php echo htmlspecialchars($row['Total_Terjual'] ?? 0) ?></td>
                                    <td><?php echo number_format($row['harga_satuan'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?php echo number_format($total_harga_menu, 0, ',', '.') ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                            
                        </tbody>
                    </table>
                    <div class="mb-3">
                        <form method="POST" action="excel_export/export_excel_menu_rs_rekap.php" target="_blank">
                            <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                            <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Cetak Data Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Pastikan library DataTable sudah di-include di halaman Anda
        let table = new DataTable('#table_laporan');
    </script>