<?php
if (session_status() !== PHP_SESSION_ACTIVE) {`r`n    session_start();`r`n}

include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$meja = (isset($_POST["meja"])) ? htmlentities($_POST["meja"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$catatan_order = (isset($_POST["catatan_order"])) ? htmlentities($_POST["catatan_order"]) : "";
$jumlah = (isset($_POST["jumlah"])) ? htmlentities($_POST["jumlah"]) : "";
$menu = (isset($_POST["menu"])) ? htmlentities($_POST["menu"]) : "";
$toko = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";



if (isset($_POST['delete_order_item'])) {
        $id_list_order = (isset($_POST["id_list_order"])) ? htmlentities($_POST["id_list_order"]) : "";
        
        $query = mysqli_query($conn, "DELETE FROM tb_list_order WHERE id_list_order = '$id_list_order'");
        if ($query) {
                echo "<script>window.location.href='../?x=orderitem&kode_order=".$kodeorder."&meja=".$meja."&pelanggan=".$pelanggan."&kios=".$toko."';</script>";
        } else {
                echo "<script>alert('Gagal menghapus item'); window.location.href='../?x=orderitem&kode_order=".$kodeorder."&meja=".$meja."&pelanggan=".$pelanggan."&kios=".$toko."';</script>";
        }
        }
?>
