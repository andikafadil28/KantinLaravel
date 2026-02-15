<?php
session_start();

include("../Database/connect.php");
$username = (isset($_POST["username"])) ? htmlentities($_POST["username"]) : "";
$password = (isset($_POST["password"])) ? htmlentities($_POST["password"]) : "";
$level = (isset($_POST["level"])) ? htmlentities($_POST["level"]) : "";
$kios = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";

$password_hash = password_hash($password, PASSWORD_DEFAULT);
if (isset($_POST['input_user_proses'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
        if (mysqli_num_rows($select_query) > 0) {
                echo "<script>alert('Username sudah terdaftar'); window.location.href='../user';</script>";
                exit();
        } else {
                $query = mysqli_query($conn, "INSERT INTO user (username, password, level, kios) VALUES ('$username', '$password_hash', '$level', '$kios')");
                if ($query) {
                        echo "<script>alert('User berhasil ditambahkan'); window.location.href='../user';</script>";
                } else {
                        echo "<script>alert('Gagal menambahkan user'); window.location.href='../user';</script>";
                }
        }
}
