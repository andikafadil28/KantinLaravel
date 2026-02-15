<?php
session_start();

include("../Database/connect.php");

$username = (isset($_POST["username"])) ? htmlentities($_POST["username"]) : "";
$password = (isset($_POST["password"])) ? $_POST["password"] : ""; // Keep the password raw for password_verify()

if (!empty($_POST['submit_validate'])) {
    // 1. Prepare the SQL statement to prevent SQL injection
    // Select the hashed password and level for the given username
    $stmt = mysqli_prepare($conn, "SELECT password, level FROM user WHERE username = $username");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // 2. Verify the password
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct
        $_SESSION['username_kantin'] = $username;
        $_SESSION['level_kantin'] = $user['level'];
        header('location:../home');
        exit(); // Always exit after a header redirect
    } else {
        // Invalid username or password
        ?>
        <script>
            alert('Username atau password Anda salah.');
            window.location = '../login';
        </script>
        <?php
    }
    mysqli_stmt_close($stmt);
}
?>