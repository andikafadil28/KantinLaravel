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


if (isset($_POST['input_order_item_proses'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_list_order WHERE kode_order = '$kodeorder' AND menu = '$menu'");
        if (mysqli_num_rows($select_query) > 0) {
                $message = "<script>alert('Item sudah terdaftar dalam order ini'); window.location.href='../?x=orderitem&kode_order=" . $kodeorder . "&meja=" . $meja . "&pelanggan=" . $pelanggan . "&kios=" . $toko . "';</script>";
                exit();
        } else {
                $query = mysqli_query($conn, "INSERT INTO tb_list_order (kode_order, menu, jumlah, catatan_order,status) VALUES ('$kodeorder', '$menu', '$jumlah', '$catatan_order','0')");
                if ($query) {
                        $message = '<script>
                        window.location.href="../?x=orderitem&kode_order=' . $kodeorder . '&meja=' . $meja . '&pelanggan=' . $pelanggan . '&kios=' . $toko . '";</script>';
                } else {
                        echo "<script>alert('Gagal menambahkan item'); window.location.href='../order';</script>";
                }
        }

}
echo $message;



