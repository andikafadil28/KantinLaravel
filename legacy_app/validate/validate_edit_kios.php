<?php
if (session_status() !== PHP_SESSION_ACTIVE) {`r`n    session_start();`r`n}

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
$nama = (isset($_POST["nama"])) ? htmlentities($_POST["nama"]) : "";


if (isset($_POST['input_kios_edit'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_kios WHERE nama = '$nama'");
        if (mysqli_num_rows($select_query) > 0) {
                echo "<script>alert('Username sudah terdaftar'); window.location.href='../user';</script>";
                exit();
        } else {
                $query = mysqli_query($conn, "update tb_kios set nama = '$nama' where id = '$_POST[id]'");
                if ($query) {
                        echo "<script>alert('User berhasil Di Update'); window.location.href='../kios';</script>";
                } else {
                        echo "<script>alert('Gagal Mengupdate user'); window.location.href='../kios';</script>";
                }
        }
}
