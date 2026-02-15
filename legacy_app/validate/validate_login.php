<?php
session_start();

include("../Database/connect.php");
$username = (isset($_POST["username"])) ? htmlentities($_POST["username"]) : "";
$password = (isset($_POST["password"])) ? htmlentities($_POST["password"]) : "";


//md5(htmlentities($_POST["password"])) : "";
if (!empty($_POST['submit_validate'])) {
      $query = mysqli_query($conn, "select * from user where username = '$username'");
      $hasil = mysqli_fetch_array($query);

      if (password_verify($password, $hasil['password'])) {
            $_SESSION['username_kantin'] = $username;
            $_SESSION['level_kantin'] = $hasil['level'];
            $_SESSION['id_kantin'] = $hasil['id'];
            $_SESSION['nama_toko_kantin'] = $hasil['Kios'];
            header('location:../home');
      } else { ?>
            <script>
                  alert('Username atau password anda salah');
                  window.location = '../login';
            </script>
            <?php
      }
     
}
?>