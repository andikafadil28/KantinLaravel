<?php
if (session_status() !== PHP_SESSION_ACTIVE) {`r`n    session_start();`r`n}

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
$username = (isset($_POST["username"])) ? htmlentities($_POST["username"]) : "";
$password = (isset($_POST["password"])) ? htmlentities($_POST["password"]) : "";
$level = (isset($_POST["level"])) ? htmlentities($_POST["level"]) : "";
$kios = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";

$password_hash = password_hash($password, PASSWORD_DEFAULT);
if (isset($_POST['input_user_edit'])) { 
          $query = mysqli_query($conn, "update user set username = '$username', password = '$password_hash', level = '$level', kios = '$kios' where id = '$_POST[id]'");
                if ($query) {
                        echo "<script>alert('User berhasil Di Update'); window.location.href='../user';</script>";
                } else {
                        echo "<script>alert('Gagal Mengupdate user'); window.location.href='../user';</script>";
                }
}   
?>
