<?php
session_start();

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
$foto = (isset($_POST["foto"])) ? htmlentities($_POST["foto"]) : "";



if (isset($_POST['input_menu_delete'])) {
      $query = mysqli_query($conn, "DELETE FROM tb_menu WHERE id = '$id'");
      unlink("../assets/img/" . $foto); // Delete the image file from the server
        if ($query) {
                echo "<script>alert('Menu berhasil Di Hapus'); window.location.href='../menu';</script>";
        } else {
                echo "<script>alert('Gagal Menghapus menu'); window.location.href='../menu';</script>";
        } 
}   
?>