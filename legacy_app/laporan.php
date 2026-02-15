<?php
include "Database/connect.php";
date_default_timezone_set("Asia/Jakarta");

// Inisialisasi variabel filter
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$kios_filter = isset($_POST['kios_filter']) ? $_POST['kios_filter'] : '';
$where_clause = "";
$query_string = "";

// Ambil data kios untuk dropdown
$query2 = mysqli_query($conn, "SELECT nama FROM tb_kios ORDER BY nama ASC");
$result2 = [];
while ($record2 = mysqli_fetch_array($query2)) {
    $result2[] = $record2;
}

// Logika penentuan query string utama berdasarkan filter
if (isset($_POST['filter'])) {
    // Sanitasi input
    $start_date_esc = mysqli_real_escape_string($conn, $start_date);
    $end_date_esc = mysqli_real_escape_string($conn, $end_date);
    $kios_filter_esc = mysqli_real_escape_string($conn, $kios_filter);

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

    // Tentukan query string dengan klausa WHERE yang baru dibuat
    $query_string = "SELECT 
                        tb_order.*, 
                        tb_bayar.id_bayar, tb_bayar.jumlah_bayar, tb_bayar.diskon, tb_bayar.nominal_toko, tb_bayar.nominal_rs 
                     FROM tb_order
                     LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
                     -- LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order -- Tidak diperlukan jika hanya mengambil data order & bayar
                     $where_clause
                     GROUP BY tb_order.id_order 
                     ORDER BY tb_order.waktu_order DESC";
} else {
    // Query default (Semua data, disarankan ditambahi limit tanggal jika data sangat banyak)
    $query_string = "SELECT 
                        tb_order.*, 
                        tb_bayar.id_bayar, tb_bayar.jumlah_bayar, tb_bayar.diskon, tb_bayar.nominal_toko, tb_bayar.nominal_rs 
                     FROM tb_order
                     LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
                     GROUP BY tb_order.id_order 
                     ORDER BY tb_order.waktu_order DESC";
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
                                <option value="" <?php echo empty($kios_filter) ? 'selected' : ''; ?> hidden>Pilih Kios</option>
                                <?php foreach ($result2 as $row2) { ?>
                                    <option value="<?php echo htmlspecialchars($row2['nama']) ?>" <?php echo $kios_filter == $row2['nama'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row2['nama']) ?></option>
                                <?php } ?>
                                <option value="all" <?php echo $kios_filter == 'all' ? 'selected' : ''; ?>>Semua Kios</option>
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
                    // Tampilkan informasi filter yang sedang diterapkan
                    $info_filter = "Menampilkan data laporan";
                    $kios_display = "Semua Kios";

                    if (!empty($kios_filter) && $kios_filter != 'all') {
                        $kios_display = "**" . htmlspecialchars($kios_filter) . "**";
                    }
                    $info_filter .= " untuk kios $kios_display";

                    $tanggal_info = [];
                    if (!empty($start_date)) {
                        $tanggal_info[] = "dari **" . htmlspecialchars($start_date) . "**";
                    }
                    if (!empty($end_date)) {
                        $tanggal_info[] = "sampai **" . htmlspecialchars($end_date) . "**";
                    }

                    if (!empty($tanggal_info)) {
                        $info_filter .= " " . implode(" ", $tanggal_info);
                    } else if (isset($_POST['filter'])) {
                        $info_filter .= " untuk **Semua Waktu**";
                    } else {
                        $info_filter = "Menampilkan data laporan **Semua Waktu dan Semua Kios** (default).";
                    }

                    echo "<p class='alert alert-info'>$info_filter</p>";
                    ?>

                    <table class="table table-hover" id="table_laporan">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kode Order</th>
                                <th scope="col">Pelanggan</th>
                                <th scope="col">Meja</th>
                                <th scope="col">Pendapatan Toko</th>
                                <th scope="col">Pendapatan Sakina Food Court</th>
                                <th scope="col">Total Bayar</th>
                                <th scope="col">Status</th>
                                <th scope="col">Diskon</th>
                                <th scope="col">Waktu Order</th>
                                <th scope="col">Nama Toko</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $id_nomor = 1;
                            foreach ($result as $row) {
                                $is_paid = !empty($row['id_bayar']);
                                $status_badge = $is_paid ? "<span class='badge text-bg-success'>Dibayar</span>" : "<span class='badge text-bg-danger'>Belum Dibayar</span>";
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $id_nomor++ ?></th>
                                    <td><?php echo htmlspecialchars($row['id_order'] ?? '-') ?></td>
                                    <td><?php echo htmlspecialchars($row['pelanggan'] ?? '-') ?></td>
                                    <td><?php echo htmlspecialchars($row['meja'] ?? '-') ?></td>
                                    <td><?php echo number_format($row['nominal_toko'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?php echo number_format($row['nominal_rs'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?php echo number_format($row['jumlah_bayar'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?php echo $status_badge ?></td>
                                    <td><?php echo number_format($row['diskon'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?php echo htmlspecialchars($row['waktu_order'] ?? '-') ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_kios'] ?? '-') ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <hr>

                    <table class="table table-striped table-bordered w-50">
                        <thead>
                            <tr class="table-primary">
                                <th colspan="2" class="text-center">REKAPITULASI TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_toko = 0;
                            $total_rs = 0;
                            $total_diskon = 0;

                            foreach ($result as $row) {
                                $total_toko += $row['nominal_toko'] ?? 0;
                                $total_rs += $row['nominal_rs'] ?? 0;
                                $total_diskon += $row['diskon'] ?? 0;
                            }
                            $grand_total = $total_toko + $total_rs + $total_diskon;
                            ?>
                            <tr>
                                <td class="fw-bold w-75">Total Pendapatan Toko</td>
                                <td class="fw-bold w-25 text-end">Rp <?php echo number_format($total_toko, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Pendapatan Sakina Food Court</td>
                                <td class="fw-bold text-end">Rp <?php echo number_format($total_rs, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Diskon</td>
                                <td class="fw-bold text-end">Rp <?php echo number_format($total_diskon, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="table-success">
                                <td class="fw-bolder">GRAND TOTAL KOTOR (Toko + Food Court + Diskon)</td>
                                <td class="fw-bolder text-end">Rp <?php echo number_format($grand_total, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mb-3">
                        <form method="POST" action="excel_export/export_excel.php" target="_blank">
                            <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                            <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                            <input type="hidden" name="kios_filter" value="<?php echo htmlspecialchars($kios_filter); ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Cetak All Data Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Memastikan DataTables diinisialisasi setelah elemen table tersedia
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof DataTable !== 'undefined') {
                let table = new DataTable('#table_laporan');
            }
        });
    </script>