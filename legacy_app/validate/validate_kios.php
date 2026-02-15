<?php
session_start();

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
$nama = (isset($_POST["nama"])) ? htmlentities($_POST["nama"]) : "";

if (isset($_POST['input_kios_proses'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_kios WHERE nama = '$nama'");
        if (mysqli_num_rows($select_query) > 0) {
                echo "<script>alert('ID sudah terdaftar'); window.location.href='../kios';</script>";
                exit();
        } else {
                $query = mysqli_query($conn, "INSERT INTO tb_kios (nama) VALUES ('$nama')");
                if ($query) {
                        echo "<script>alert('Kios berhasil ditambahkan'); window.location.href='../kios';</script>";
                } else {
                        echo "<script>alert('Gagal menambahkan Kios'); window.location.href='../kios';</script>";
                }
        }
}
