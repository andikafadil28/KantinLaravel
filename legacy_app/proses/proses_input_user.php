<?php
include("../Database/connect.php");
$username = (isset($_POST["username"])) ? htmlentities($_POST["username"]) : "";
$password = (isset($_POST["password"])) ? md5(htmlentities($_POST["password"])) : "";
$level = (isset($_POST["level"])) ? htmlentities($_POST["level"]) : "";
$kios = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";

$message = "";
if (!empty($_POST['input_user_proses'])) {
      $query = mysqli_query($conn, "INSERT INTO user (username, password, level, kios) 
      VALUES ('$id', '$password', '$level', '$kios')");
      if (!$query) {
            $message = '<script>alert("Gagal menambahkan data user");</script>';
      } else {
            $message = '<script>alert("Gagal menambahkan data user");</script>';
      }     
}echo $message;

?>
