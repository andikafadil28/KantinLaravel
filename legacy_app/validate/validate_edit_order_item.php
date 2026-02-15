<?php
session_start();

include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$meja = (isset($_POST["meja"])) ? htmlentities($_POST["meja"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$catatan_order = (isset($_POST["catatan_order"])) ? htmlentities($_POST["catatan_order"]) : "";
$jumlah = (isset($_POST["jumlah"])) ? htmlentities($_POST["jumlah"]) : "";
$menu = (isset($_POST["menu"])) ? htmlentities($_POST["menu"]) : "";
$toko = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";

if (isset($_POST['edit_order_item'])) {
        $select_query = mysqli_query($conn, "UPDATE tb_list_order SET menu = '$menu', jumlah = '$jumlah', catatan_order = '$catatan_order' WHERE kode_order = '$kodeorder' AND menu = '$menu'");
        if ($select_query) {
                $message = '<script>window.location.href="../?x=orderitem&kode_order='.$kodeorder.'&meja='.$meja.'&pelanggan='.$pelanggan.'&kios='.$toko.'";</script>';
        } else {
                echo "<script>alert('Gagal memperbarui item'); window.location.href='../order';</script>";
        }       
}
echo $message;


