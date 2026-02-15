<?php
if (session_status() !== PHP_SESSION_ACTIVE) {`r`n    session_start();`r`n}

include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$meja = (isset($_POST["meja"])) ? htmlentities($_POST["meja"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$kios = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";
$catatan = (isset($_POST["catatan"])) ? htmlentities($_POST["catatan"]) : "";

if (isset($_POST['input_order_edit'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_order WHERE id_order = '$kodeorder'");
        
                $query = mysqli_query($conn, "UPDATE tb_order SET meja = '$meja', pelanggan = '$pelanggan', nama_kios = '$kios', catatan = '$catatan' WHERE id_order = '$kodeorder'");
                if ($query) {
                        $message =  '<script>alert("Order berhasil ditambahkan"); 
                        window.location.href="../order";</script>';
                } else {
                        echo "<script>alert('Gagal menambahkan order'); window.location.href='../order';</script>";
                }
        
}
echo $message;


