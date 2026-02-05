<?php
include "Database/connect.php";
date_default_timezone_set("Asia/Jakarta");

// Inisialisasi variabel
$where_clause = "";
$start_date = "";
$end_date = "";
$kios_filter = ""; // Tambahkan inisialisasi untuk kios_filter

// Ambil data kios untuk dropdown
$query2 = mysqli_query($conn, "select * from tb_kios ORDER BY nama ASC");
$result2 = [];
while ($record2 = mysqli_fetch_array($query2)) {
    $result2[] = $record2;
}

// Logika penentuan query string utama
if (isset($_POST['filter'])) {
    // Ambil input dari form dan lakukan sanitasi
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $kios_filter = mysqli_real_escape_string($conn, $_POST['kios_filter']);
    
    // Tentukan klausa WHERE berdasarkan filter
    $where_parts = [];
    
    // 1. Filter Kios
    if (!empty($kios_filter) && $kios_filter != 'all') {
        $where_parts[] = "tb_order.nama_kios = '$kios_filter'";
    }

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
    
    // Query untuk semua filter (atau filter tanggal saja, atau filter kios saja)
    $query_string = "SELECT tb_order.waktu_order, tb_menu.nama, sum(tb_list_order.jumlah) AS Total_Terjual, tb_menu.pajak AS harga_satuan, SUM(tb_list_order.jumlah)*tb_menu.pajak as Total_harga, tb_menu.nama_toko 
                    FROM tb_order
                    LEFT JOIN user ON user.id = tb_order.kasir
                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                    $where_clause
                    GROUP BY tb_menu.nama, tb_menu.nama_toko
                    ORDER BY tb_menu.nama_toko ASC, tb_menu.nama ASC";

} else {
    // Query default (tampilkan semua data tanpa filter tanggal/kios)
    $query_string = "SELECT tb_order.waktu_order, tb_menu.nama, sum(tb_list_order.jumlah) AS Total_Terjual, tb_menu.pajak AS harga_satuan, SUM(tb_list_order.jumlah)*tb_menu.pajak as Total_harga, tb_menu.nama_toko 
                    FROM tb_order
                    LEFT JOIN user ON user.id = tb_order.kasir
                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                    GROUP BY tb_menu.nama, tb_menu.nama_toko
                    ORDER BY tb_menu.nama_toko ASC, tb_menu.nama ASC";
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
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="kios_filter" class="form-label">Nama Kios</label>
                            <select class="form-select" aria-label="Default select example" name="kios_filter">
                                <option value="" <?php echo empty($kios_filter) ? 'selected' : ''; ?> hidden>Pilih Kios User</option>
                                <?php
                                foreach ($result2 as $row2) {
                                ?>
                                    <option value="<?php echo htmlspecialchars($row2['nama']) ?>" <?php echo $kios_filter == $row2['nama'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row2['nama']) ?></option>
                                <?php
                                }
                                ?>
                                <option value="all" <?php echo $kios_filter == 'all' ? 'selected' : ''; ?>>all</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
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
                    if (isset($_POST['filter'])) {
                        $info_filter = "Menampilkan data laporan";
                        if (!empty($kios_filter) && $kios_filter != 'all') {
                            $info_filter .= " untuk kios **" . htmlspecialchars($kios_filter) . "**";
                        } else if ($kios_filter == 'all') {
                            $info_filter .= " untuk **Semua Kios**";
                        }
                        
                        $tanggal_info = [];
                        if (!empty($start_date)) {
                            $tanggal_info[] = "dari **" . htmlspecialchars($start_date) . "**";
                        }
                        if (!empty($end_date)) {
                            $tanggal_info[] = "sampai **" . htmlspecialchars($end_date) . "**";
                        }
                        
                        if (!empty($tanggal_info)) {
                             $info_filter .= " " . implode(" ", $tanggal_info);
                        }
                        
                        echo "<p class='alert alert-info'>$info_filter</p>";
                    } else {
                        echo "<p class='alert alert-info'>Menampilkan data laporan **Semua Waktu dan Semua Kios** (default).</p>";
                    }

                    ?>
                    
                    <table class="table table-hover" id="table_laporan">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Menu</th>
                                <th scope="col">Nama Toko</th>
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
                                    <td><?php echo htmlspecialchars($row['nama_toko'] ?? '') ?></td>
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
                        <form method="POST" action="excel_export/export_excel_menu_rs.php" target="_blank">
                            <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                            <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                            <input type="hidden" name="kios_filter" value="<?php echo htmlspecialchars($kios_filter); ?>">
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
        let table = new DataTable('#table_laporan');
    </script>