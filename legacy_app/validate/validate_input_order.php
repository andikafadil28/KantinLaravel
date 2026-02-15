<?php
session_start();

include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$meja = (isset($_POST["meja"])) ? htmlentities($_POST["meja"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$kios = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";
$catatan = (isset($_POST["catatan"])) ? htmlentities($_POST["catatan"]) : "";

if (isset($_POST['input_order_proses'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_order WHERE id_order = '$kodeorder'");
        if (mysqli_num_rows($select_query) > 0) {
                echo "<script>alert('Kode order sudah terdaftar'); window.location.href='../order';</script>";
                exit();
        } else {
                $query = mysqli_query($conn, "INSERT INTO tb_order (id_order, meja, pelanggan, nama_kios, catatan,kasir) VALUES ('$kodeorder', '$meja', '$pelanggan', '$kios', '$catatan', '$_SESSION[id_kantin]')");
                if ($query) {
                        $message =  '<script> window.location.href="../?x=orderitem&kode_order='.$kodeorder.'&meja='.$meja.'&pelanggan='.$pelanggan.'&kios='.$kios.'";</script>';
                } else {
                        echo "<script>alert('Gagal menambahkan order'); window.location.href='../order';</script>";
                }
        }
}
echo $message;


