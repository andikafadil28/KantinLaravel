<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
}

include("../Database/connect.php");
$username = (isset($_POST["username"])) ? trim((string) $_POST["username"]) : "";
$password = (isset($_POST["password"])) ? htmlentities($_POST["password"]) : "";


//md5(htmlentities($_POST["password"])) : "";
if (!empty($_POST['submit_validate'])) {
      $usernameEscaped = mysqli_real_escape_string($conn, $username);
      $query = mysqli_query($conn, "select * from user where username = '$usernameEscaped'");
      $hasil = mysqli_fetch_array($query);

      if ($hasil && isset($hasil['password']) && password_verify($password, (string) $hasil['password'])) {
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
