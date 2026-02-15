<?php
if (session_status() !== PHP_SESSION_ACTIVE) {`r`n    session_start();`r`n}

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";


$password_hash = password_hash($password, PASSWORD_DEFAULT);
if (isset($_POST['input_user_delete'])) {
      $query = mysqli_query($conn, "DELETE FROM user WHERE id = '$id'");
        if ($query) {
                echo "<script>alert('User berhasil Di Hapus'); window.location.href='../user';</script>";
        } else {
                echo "<script>alert('Gagal Menghapus user'); window.location.href='../user';</script>";
        }
}   
?>
