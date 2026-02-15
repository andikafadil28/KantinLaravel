<?php
include "Database/connect.php";
$query = mysqli_query($conn, "select * from user");
while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}
$query2 = mysqli_query($conn, "select * from tb_kios");
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
            <div class="row">
                <div class="col d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambah">Tambah User</button>
                </div>
                <!-- Modal tambah user -->
                <div class="modal fade" id="ModalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah User</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" novalidate action="validate/validate_user.php" method="post">
                                    <div class="row">
                                        <div class="col lg-6">
                                            <div class="form-floating ">
                                                <input type="text" class="form-control" id="floatingInput" placeholder="Masukan ID" name="username" required>
                                                <label for="floatingInput">nama</label>
                                                <div class="invalid-feedback">
                                                    Nama tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col lg-6">
                                            <div class="form-floating ">
                                                <input type="password" class="form-control" id="floatingPassword" placeholder="Masukan Password" name="password" required>
                                                <label for="floatingPassword">Password</label>
                                                <div class="invalid-feedback">
                                                    Password tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col lg-6">
                                            <div class="form-floating ">
                                                <select class="form-select" aria-label="Default select example" name="level" required>
                                                    <option selected hidden>Pilih Level User</option>
                                                    <option value="1">Admin</option>
                                                    <option value="2">Kasir</option>
                                                    <option value="3">Pemilik Kios</option>
                                                </select>
                                                <label for="floatinglevel">Level User</label>
                                                <div class="invalid-feedback">
                                                    Level User tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col lg-6">
                                            <div class="form-floating ">
                                                <select class="form-select" aria-label="Default select example" name="kios" required>
                                                    <option selected>Pilih Kios User</option>
                                                    <?php
                                                    foreach ($result2 as $row2) {
                                                    ?>
                                                        <option value="<?php echo $row2['nama'] ?>"><?php echo $row2['nama'] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <label for="floatingkios">Nama Kios</label>
                                                <div class="invalid-feedback">
                                                    Kios tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="input_user_proses">Simpan</button>
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
                    <div class="modal fade" id="ModalEdit<?php echo $row['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_edit.php" method="post">
                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                        <div class="row">
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <input type="text" class="form-control" id="floatingInput" placeholder="Masukan ID" name="username" value="<?php echo $row['username'] ?>" required>
                                                    <label for="floatingInput">nama</label>
                                                </div>
                                            </div>
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <input type="password" class="form-control" id="floatingPassword" placeholder="Masukan Password" name="password" required>
                                                    <label for="floatingPassword">Password</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <div class="form-floating ">
                                                        <!-- <input type="text" class="form-control" id="floatingInput" placeholder="level" name="level"
                                                    value=""> -->
                                                        <select class="form-select" aria-label="Default select example" name="level" required>
                                                            <option selected hidden><?php
                                                                                    if ($row['level'] == 1) {
                                                                                        echo "Admin";
                                                                                    } elseif ($row['level'] == 2) {
                                                                                        echo "Kasir";
                                                                                    } elseif ($row['level'] == 3) {
                                                                                        echo "Pemilik Kios";
                                                                                    } else {
                                                                                        echo "Unknown";
                                                                                    }
                                                                                    ?></option>
                                                            <option value="1">Admin</option>
                                                            <option value="2">Kasir</option>
                                                            <option value="3">Pemilik Kios</option>
                                                        </select>
                                                        <label for="floatingInput">Level</label>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <!-- <input type="text" class="form-control" id="floatingkios" placeholder="Masukan Kios" name="kios" value=""> -->
                                                    <select class="form-select" aria-label="Default select example" name="kios" required>
                                                        <option selected hidden><?php echo $row['Kios'] ?></option>
                                                        <?php
                                                        foreach ($result2 as $row2) {
                                                        ?>
                                                            <option value="<?php echo $row2['nama'] ?>"><?php echo $row2['nama'] ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="floatingkios">Nama Kios</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" name="input_user_edit">Simpan Data</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal delete -->
                    <div class="modal fade" id="ModalDelete<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_delete.php" method="post">
                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                        <div class="col-lg-12">
                                            <?php
                                            if ($row['username'] == $_SESSION['username_kantin']) {
                                                echo "<div class='alert alert-danger'>Anda tidak dapat menghapus user ini karena sedang login sebagai user tersebut.</div>";
                                            } else {
                                                echo "Apakah anda yakin ingin menghapus user ini <b>$row[username]</b>?
                                <p>Data yang sudah dihapus tidak dapat dikembalikan lagi.</p>";
                                            }
                                            ?>

                                        </div>


                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger" name="input_user_delete" <?php echo ($row['username'] == $_SESSION['username_kantin']) ? 'disabled' : ''; ?>>Hapus Data</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Modal view -->
                    <div class="modal fade" id="ModalView<?php echo $row['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">View</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="validate/validate_user.php">
                                        <div class="row">
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <input type="text" class="form-control" id="floatingInput" placeholder="Masukan ID" name="username" value="<?php echo $row['username'] ?>" readonly>
                                                    <label for="floatingInput">nama</label>
                                                </div>
                                            </div>
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <input type="password" class="form-control" id="floatingPassword" placeholder="Masukan Password" name="password" value="<?php echo $row['password'] ?>" readonly>
                                                    <label for="floatingPassword">Password</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <div class="form-floating ">
                                                        <input type="text" class="form-control" id="floatingInput" placeholder="level" name="level"
                                                            value="<?php
                                                                    if ($row['level'] == 1) {
                                                                        echo "Admin";
                                                                    } elseif ($row['level'] == 2) {
                                                                        echo "Kasir";
                                                                    } elseif ($row['level'] == 3) {
                                                                        echo "Pemilik Kios";
                                                                    } else {
                                                                        echo "Unknown";
                                                                    }
                                                                    ?>" readonly>
                                                        <label for="floatingInput">Level</label>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col lg-6">
                                                <div class="form-floating ">
                                                    <input type="text" class="form-control" id="floatingkios" placeholder="Masukan Kios" name="kios" value="<?php echo $row['Kios'] ?>" readonly>
                                                    <label for="floatingkios">Nama Kios</label>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                        <table class="table table-hover" id="table_user">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Password</th>
                                    <th scope="col">Level User</th>
                                    <th scope="col">Kios</th>
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
                                        <td><?php echo $row['username'] ?></td>
                                        <td><?php echo $row['password'] ?></td>
                                        <td><?php
                                            if ($row['level'] == 1) {
                                                echo "Admin";
                                            } elseif ($row['level'] == 2) {
                                                echo "Kasir";
                                            } elseif ($row['level'] == 3) {
                                                echo "Pemilik Kios";
                                            } else {
                                                echo "Unknown";
                                            }
                                            ?></td>
                                        <td><?php echo $row['Kios'] ?></td>
                                        <?php
                                        if ($_SESSION["level_kantin"] == 1) {
                                        ?>
                                            <td class="d-flex">
                                                <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#ModalView<?php echo $row['id'] ?>"> <i class="bi bi-eye-fill"></i></button>
                                                <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['id'] ?>"> <i class="bi bi-pencil-fill"></i></button>
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['id'] ?>"> <i class="bi bi-trash-fill"></i></button>
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
<!-- End of Conten -->
 <!-- <script> 
 Toastify({
  text: "This is a toast",
  className: "info",
  style: {
    background: "linear-gradient(to right, #00b09b, #96c93d)",
  }
}).showToast();
 
 </script> -->
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
    let table = new DataTable('#table_user');

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