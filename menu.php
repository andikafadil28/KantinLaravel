<?php
include "Database/connect.php";
$query = mysqli_query($conn, "select * from tb_menu
LEFT JOIN tb_kategori_menu ON tb_kategori_menu.id_kategori = tb_menu.kategori");
$sel_kategori = mysqli_query($conn, "SELECT id_kategori,kategori_menu FROM tb_kategori_menu");
$query2 = mysqli_query($conn, "select * from tb_kios");
while ($record2 = mysqli_fetch_array($query2)) {
    $result2[] = $record2;
}
while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}

?>

<!-- Conten -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-fork-knife"></i>
            Menu
        </div>
        <div class="card-body-scrollable">
            <div class="row">
                <?php
                if ($_SESSION["level_kantin"] == 1) {
                    ?>
                    <div class="col d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambah">Tambah
                            Menu</button>
                    </div>
                    <?php
                } else {
                }
                ?>

                <!-- Modal tambah menu -->
                <div class="modal fade" id="ModalTambah" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Makanan Dan Minuman</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" novalidate action="validate/validate_menu.php"
                                    method="post" enctype="multipart/form-data">
                                    <div class="row mt-3">
                                        <div class="col lg-12">
                                            <div class="input-group">
                                                <input type="file" class="form-control py-9" id="floatingInputGambar"
                                                    placeholder="Masukan Gambar" name="foto" required>
                                                <label class="input-group-text" for="floatingInputGambar">Upload Foto
                                                    Menu</label>
                                                <div class="invalid-feedback">
                                                    Gambar tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="floatingNama"
                                                    placeholder="Masukan Nama" name="nama_menu" required>
                                                <label for="floatingNama">Nama Makanan</label>
                                                <div class="invalid-feedback">
                                                    Nama tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="floatingKeterangan"
                                                    placeholder="Masukan Keterangan" name="keterangan">
                                                <label for="floatingKeterangan">Keterangan</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col lg-4">
                                            <div class="form-floating mt-3">
                                                <select class="form-select" aria-label="Default select example"
                                                    name="kategori_menu" required>
                                                    <option selected hidden value="">Pilih Jenis Menu</option>
                                                    <?php
                                                    foreach ($sel_kategori as $row2) {
                                                        ?>
                                                        <option value="<?php echo $row2['id_kategori'] ?>">
                                                            <?php echo $row2['kategori_menu'] ?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <label for="floatingKategori">Kategori Menu</label>
                                                <div class="invalid-feedback">
                                                    Jenis Menu tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col lg-4">
                                            <div class="form-floating mt-3">
                                                <input type="number" class="form-control" id="floatingHarga"
                                                    placeholder="Masukan Harga" name="harga" required>
                                                <label for="floatingHarga">Harga</label>
                                                <div class="invalid-feedback">
                                                    Harga tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col lg-4">
                                            <div class="form-floating mt-3">
                                                <input type="number" class="form-control" id="floatingStok"
                                                    placeholder="Masukan Stok" name="pajak" required>
                                                <label for="floatingStok">Margin</label>
                                                <div class="invalid-feedback">
                                                    Margin tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="form-floating mt-3">
                                                <select class="form-select" aria-label="Default select example"
                                                    name="kios" required>
                                                    <option selected hidden value="">Pilih Kios User</option>
                                                    <?php
                                                    foreach ($result2 as $row2) {
                                                        ?>
                                                        <option value="<?php echo $row2['nama'] ?>">
                                                            <?php echo $row2['nama'] ?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <label for="floatingKios">Kios</label>
                                                <div class="invalid-feedback">
                                                    Kios tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary"
                                            name="input_menu_proses">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php

                foreach ($result as $row) {
                    ?>
                    <!-- Modal edit -->
                    <div class="modal fade" id="ModalEdit<?php echo $row['id'] ?>" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Makanan Dan Minuman</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_menu_edit.php"
                                        method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                        <div class="row mt-3">
                                            <div class="col lg-12">
                                                <div class="input-group">
                                                    <input type="file" class="form-control py-9" id="floatingInputGambar"
                                                        placeholder="Masukan Gambar" name="foto">
                                                    <label class="input-group-text" for="floatingInputGambar">Upload Foto
                                                        Menu</label>
                                                    <div class="invalid-feedback">
                                                        Gambar tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingNama"
                                                        placeholder="Masukan Nama" name="nama_menu"
                                                        value="<?php echo $row['nama'] ?>" required>
                                                    <label for="floatingNama">Nama Makanan</label>
                                                    <div class="invalid-feedback">
                                                        Nama tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingKeterangan"
                                                        placeholder="Masukan Keterangan" name="keterangan"
                                                        value="<?php echo $row['keterangan'] ?>">
                                                    <label for="floatingKeterangan">Keterangan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col lg-4">
                                                <div class="form-floating mt-3">
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="kategori_menu" required>
                                                        <option selected hidden value="">Pilih Jenis Menu</option>
                                                        <?php
                                                        foreach ($sel_kategori as $row2) {
                                                            if ($row['kategori'] == $row2['id_kategori']) {
                                                                echo "<option selected value='" . $row2['id_kategori'] . "'>" . $row2['kategori_menu'] . "</option>";
                                                            } else {
                                                                echo "<option value='" . $row2['id_kategori'] . "'>" . $row2['kategori_menu'] . "</option>";
                                                            }
                                                            ?>

                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="floatingKategori">Kategori Menu</label>
                                                    <div class="invalid-feedback">
                                                        Jenis Menu tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col lg-4">
                                                <div class="form-floating mt-3">
                                                    <input type="number" class="form-control" id="floatingHarga"
                                                        placeholder="Masukan Harga" name="harga"
                                                        value="<?php echo $row['harga'] ?>" required>
                                                    <label for="floatingHarga">Harga</label>
                                                    <div class="invalid-feedback">
                                                        Harga tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col lg-4">
                                                <div class="form-floating mt-3">
                                                    <input type="number" class="form-control" id="floatingStok"
                                                        placeholder="Masukan Stok" name="pajak"
                                                        value="<?php echo $row['pajak'] ?>" required>
                                                    <label for="floatingStok">Margin</label>
                                                    <div class="invalid-feedback">
                                                        Marjin tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating mt-3">
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="kios" required>
                                                        <option selected hidden value="">Pilih Kios User</option>
                                                        <?php
                                                        foreach ($result2 as $row2) {
                                                            ?>
                                                            <option value="<?php echo $row2['nama'] ?>" <?php echo ($row['nama_toko'] == $row2['nama']) ? 'selected' : ''; ?>>
                                                                <?php echo $row2['nama'] ?>
                                                            </option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="floatingKios">Kios</label>
                                                    <div class="invalid-feedback">
                                                        Kios tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary"
                                                name="input_menu_edit_proses">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal delete -->
                    <div class="modal fade" id="ModalDelete<?php echo $row['id'] ?>" tabindex="-1"
                        aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_menu_delete.php"
                                        method="post">
                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                        <input type="hidden" name="foto" value="<?php echo $row['foto'] ?>">
                                        <div class="col-lg-12">

                                            <h5>Apakah Anda yakin ingin menghapus menu
                                                <strong><?php echo $row['nama'] ?></strong>?
                                            </h5>
                                            <p>Data yang dihapus tidak dapat dikembalikan.</p>

                                        </div>


                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger" name="input_menu_delete">Hapus
                                                Data</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Modal view -->
                    <div class="modal fade" id="ModalView<?php echo $row['id'] ?>" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Makanan Dan Minuman</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_menu.php"
                                        method="post" enctype="multipart/form-data">
                                        <div class="row mt-3">

                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingNama"
                                                        placeholder="Masukan Nama" name="nama_menu"
                                                        value="<?php echo $row['nama'] ?>" disabled>
                                                    <label for="floatingNama">Nama Makanan</label>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingKeterangan"
                                                        placeholder="Masukan Keterangan" name="keterangan"
                                                        value="<?php echo $row['keterangan'] ?>" disabled>
                                                    <label for="floatingKeterangan">Keterangan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col lg-4">
                                                <div class="form-floating mt-3">
                                                    <select disabled class="form-select" aria-label="Default select example"
                                                        name="kategori_menu" required>
                                                        <option selected hidden value="">Pilih Jenis Menu</option>
                                                        <?php
                                                        foreach ($sel_kategori as $row2) {
                                                            if ($row['kategori'] == $row2['id_kategori']) {
                                                                echo "<option selected value='" . $row2['kategori'] . "'>" . $row2['kategori_menu'] . "</option>";
                                                            } else {
                                                                echo "<option value='" . $row2['kategori'] . "'>" . $row2['kategori_menu'] . "</option>";
                                                            }
                                                            ?>

                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="floatingKategori">Kategori Menu</label>
                                                    <div class="invalid-feedback">
                                                        Jenis Menu tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col lg-4">
                                                <div class="form-floating mt-3">
                                                    <input type="number" class="form-control" id="floatingHarga"
                                                        placeholder="Masukan Harga" name="harga"
                                                        value="<?php echo $row['harga'] ?>" disabled>
                                                    <label for="floatingHarga">Harga</label>
                                                    <div class="invalid-feedback">
                                                        Harga tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col lg-4">
                                                <div class="form-floating mt-3">
                                                    <input type="number" class="form-control" id="floatingStok" placeholder="Masukan Stok" name="pajak" value="<?php echo $row['pajak'] ?>" disabled>
                                                    <label for="floatingStok">Margin</label>
                                                    <div class="invalid-feedback">
                                                        Margin tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating mt-3">
                                                    <select disabled class="form-select" aria-label="Default select example"
                                                        name="kios" required>
                                                        <option selected hidden value="">Pilih Kios User</option>
                                                        <?php
                                                        foreach ($result2 as $row2) {
                                                            ?>
                                                            <option value="<?php echo $row2['nama'] ?>" <?php echo ($row['nama_toko'] == $row2['nama']) ? 'selected' : ''; ?>>
                                                                <?php echo $row2['nama'] ?>
                                                            </option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="floatingKios">Kios</label>
                                                    <div class="invalid-feedback">
                                                        Kios tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary"
                                                name="input_menu_proses">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (empty($result)) {
                    echo "<div class='alert alert-warning'>Data tidak ditemukan</div>";
                } else {
                    ?>
                    <div class="table-responsive-lg-12">
                        <table class="table table-hover" id="table_menu">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Foto Menu</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Jenis Menu</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Harga</th>
                                    <th scope="col">Nama Toko</th>
                                    <?php
                                    if ($_SESSION["level_kantin"] == 1) {
                                        ?>
                                        <th scope="col">Aksi</th>
                                        <?php
                                    } else {
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id_nomor = 1;
                                foreach ($result as $row) {
                                    ?>
                                    <tr>
                                        <th scope="row"><?php echo $id_nomor++ ?></th>
                                        <td>
                                            <div style="width: 100px;">
                                                <img src="assets/img/<?php echo $row['foto'] ?>" class="img-thumbnail"
                                                    alt="...">
                                            </div>
                                        </td>
                                        <td><?php echo $row['nama'] ?></td>
                                        <td><?php echo $row['keterangan'] ?></td>
                                        <td><?php echo ($row['jenis_menu'] == 1) ? "Makanan" : "Minuman" ?></td>
                                        <td><?php echo $row['kategori_menu'] ?></td>
                                        <td><?php echo $row['harga'] + $row['pajak'] ?></td>
                                        <td><?php echo $row['nama_toko'] ?></td>
                                        <?php
                                        if ($_SESSION["level_kantin"] == 1) {
                                            ?>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal"
                                                        data-bs-target="#ModalView<?php echo $row['id'] ?>"> <i
                                                            class="bi bi-eye-fill"></i></button>
                                                    <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal"
                                                        data-bs-target="#ModalEdit<?php echo $row['id'] ?>"> <i
                                                            class="bi bi-pencil-fill"></i></button>
                                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#ModalDelete<?php echo $row['id'] ?>"> <i
                                                            class="bi bi-trash-fill"></i></button>
                                                </div>

                                            </td>
                                            <?php
                                        } else {
                                        }
                                        ?>

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
    let table = new DataTable('#table_menu');
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