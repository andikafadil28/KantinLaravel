<?php

include "Database/connect.php";
date_default_timezone_set("Asia/Jakarta");
$where_clause = "";
$kios_filter = "";
// $sel_kategori = mysqli_query($conn, "SELECT id_kategor i,kategori_menu FROM tb_kategori_menu");
$query2 = mysqli_query($conn, "select * from tb_kios");


// var_dump($result);
// exit();
while ($record2 = mysqli_fetch_array($query2)) {
    $result2[] = $record2;
}


?>

<!-- Conten -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-fork-knife"></i>
            Setingan User
        </div>
        <div class="card-body-scrollable">
            <div>
                <form method="POST">
                   <div class="row g-3 mb-3">
                        <div class="col-12 col-md-3">
                            <label for="kios_filter" class="form-label">Nama Kios</label>
                            <select class="form-select" aria-label="Default select example" name="kios_filter">
                                <option selected hidden>Pilih</option>
                                <option value="all">All</option>
                                <?php
                                foreach ($result2 as $row2) {
                                ?>
                                    <option value="<?php echo $row2['nama'] ?>"><?php echo $row2['nama'] ?></option>
                                <?php
                                }
                                ?>

                            </select>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" name="filter" value="filter">Filter</button>
                        </div>
                        <div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                <?php

                // Logika Query SQL
                if (isset($_POST['filter']) && isset($_POST['kios_filter']) && $_POST['kios_filter'] === 'all') {
                    // Filter untuk SEMUA kios
                    $query_string = "SELECT *, SUM((harga+pajak)*jumlah) AS harganya from tb_order
                                    LEFT JOIN user ON user.id = tb_order.kasir
                                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                                    GROUP BY tb_order.id_order ORDER BY tb_order.waktu_order DESC";
                } else if (isset($_POST['filter']) && isset($_POST['kios_filter'])) {
                    // Filter untuk kios spesifik
                    $kios_filter = mysqli_real_escape_string($conn, $_POST['kios_filter']);
                    $query_string = "SELECT *, SUM((harga+pajak)*jumlah) AS harganya from tb_order
                                    LEFT JOIN user ON user.id = tb_order.kasir
                                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                                    WHERE tb_order.nama_kios = '$kios_filter'
                                    GROUP BY tb_order.id_order ORDER BY tb_order.waktu_order DESC";
                } else if($_SESSION["level_kantin"] == 3){
                    $kios_filter = $_SESSION['nama_toko_kantin'];
                    $query_string = "SELECT *, SUM((harga+pajak)*jumlah) AS harganya from tb_order
                                    LEFT JOIN user ON user.id = tb_order.kasir
                                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                                    WHERE tb_order.nama_kios = '$kios_filter'
                                    GROUP BY tb_order.id_order ORDER BY tb_order.waktu_order DESC";

                }else{
                    // Query default (tampilkan semua data tanpa filter)
                    $query_string = "SELECT *, SUM((harga+pajak)*jumlah) AS harganya from tb_order
                                    LEFT JOIN user ON user.id = tb_order.kasir
                                    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
                                    LEFT JOIN tb_menu ON tb_menu.id = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_list_order.kode_order
                                    GROUP BY tb_order.id_order ORDER BY tb_order.waktu_order DESC";
                }

                // Eksekusi Query
                $query = mysqli_query($conn, $query_string);
                if (!$query) {
                    die("Query Error: " . mysqli_error($conn));
                }

                $result = [];
                while ($record = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                    $result[] = $record;
                }


                ?>
                <div class="col d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambah">Tambah
                        Order</button>
                </div>
                <?php
                include "inc/modal/modal_order.php"
                ?>
                <?php
                if (empty($result)) {
                } else {
                ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="table_order">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kode Order</th>
                                    <th scope="col">Pelanggan</th>
                                    <th scope="col">Meja</th>
                                    <th scope="col">Diskon</th>
                                    <th scope="col">Total Harga</th>
                                    <th scope="col">Kasir</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Waktu Order</th>
                                    <th scope="col">Nama Toko</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id_nomor = 1;
                                foreach ($result as $row) {
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $id_nomor++ ?></th>
                                        <td><?php echo $row['id_order'] ?></td>
                                        <td><?php echo $row['pelanggan'] ?></td>
                                        <td><?php echo $row['meja'] ?></td>
                                        <td><?php echo number_format($row['diskon']) ?></td>
                                        <td><?php echo number_format($row['harganya'] - $row['diskon'] + $row['ppn'], 0, ',', '.') ?></td>
                                        <td><?php echo $row['username'] ?></td>
                                        <td><?php echo (!empty($row['id_bayar'])) ? "<span class='badge text-bg-success'>Dibayar</span>" : "<span class='badge text-bg-danger'>Belum Dibayar</span>"; ?>
                                        </td>
                                        <td><?php echo $row['waktu_order'] ?></td>
                                        <td><?php echo $row['nama_kios'] ?></td>
                                        <td>
                                            <div class="d-flex">
                                                <?php
                                                if ($_SESSION["level_kantin"] == 1) {

                                                ?>
                                                    <a class="btn btn-info btn-sm me-2"
                                                        href="./?x=orderitem&kode_order=<?php echo $row['id_order'] . "&meja=" . $row['meja'] . "&pelanggan=" . $row['pelanggan'] . "&kios=" . $row['nama_kios']; ?>&diskon=<?php echo (empty($row['diskon'])) ? 0 : $row['diskon']; ?>"><i
                                                            class="bi bi-eye-fill"></i></a>
                                                    <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal"
                                                        data-bs-target="#ModalEdit<?php echo $row['id_order'] ?>"> <i
                                                            class="bi bi-pencil-fill"></i></button>
                                                    <button class="btn btn-danger btn-sm me-2" data-bs-toggle="modal"
                                                        data-bs-target="#ModalDelete<?php echo $row['id_order'] ?>"> <i
                                                            class="bi bi-trash-fill"></i></button>
                                                <?php
                                                } else {
                                                ?>
                                                    <a class="btn btn-info btn-sm me-2"
                                                        href="./?x=orderitem&kode_order=<?php echo $row['id_order'] . "&meja=" . $row['meja'] . "&pelanggan=" . $row['pelanggan'] . "&kios=" . $row['nama_kios']; ?>&diskon=<?php echo (empty($row['diskon'])) ? 0 : $row['diskon']; ?>"><i
                                                            class="bi bi-eye-fill"></i></a>
                                                    <button
                                                        class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-2 disabled" : "btn btn-warning btn-sm me-2 "; ?> "
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#ModalEdit<?php echo $row['id_order'] ?>"> <i
                                                            class="bi bi-pencil-fill"></i></button>
                                                    <button
                                                        class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-2 disabled" : "btn btn-danger btn-sm me-2"; ?> "
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#ModalDelete<?php echo $row['id_order'] ?>"> <i
                                                            class="bi bi-trash-fill"></i></button>
                                                <?php
                                                }
                                                ?>
                                                <!-- <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#ModalView<?php echo $row['id_order'] ?>"> <i class="bi bi-eye-fill"></i></button> -->

                                            </div>

                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }
                ?>


            </div>





        </div>


    </div>
</div>
<script>
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
<script>
    let table = new DataTable('#table_order');
</script>

<style>
    /* Include the CSS here or link to an external stylesheet */
    .card {
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        width: 97%;
        /* Example width for the card */
        margin: 20px;
        display: flex;
        flex-direction: column;
    }

    .card-header {
        background-color: #f0f0f0;
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        font-weight: bold;
    }

    .card-body-scrollable {
        overflow-x: auto;
        /* Adds horizontal scrollbar when content overflows */
        padding: 15px;
        /* white-space: nowrap; /* Uncomment if you want text to stay on one line */
    }

    .long-content {
        min-width: 800px;
        /* Ensure content is wide enough to trigger scroll */
        /* Adjust this value based on your content's natural width */
    }
</style>