<?php
session_start();

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
$nama_menu = (isset($_POST["nama_menu"])) ? htmlentities($_POST["nama_menu"]) : "";
$keterangan = (isset($_POST["keterangan"])) ? htmlentities($_POST["keterangan"]) : "";
$kategori_menu = (isset($_POST["kategori_menu"])) ? htmlentities($_POST["kategori_menu"]) : "";
$harga = (isset($_POST["harga"])) ? htmlentities($_POST["harga"]) : "";
$stok = (isset($_POST["stok"])) ? htmlentities($_POST["stok"]) : "";
$kios = (isset($_POST["kios"])) ? htmlentities($_POST["kios"]) : "";

$kode_rand = rand(1000, 9999) . "-";
$target_dir = "../assets/img/" . $kode_rand;
$target_file = $target_dir . basename($_FILES["foto"]["name"]);
$imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


echo $imageType;

if (isset($_POST['input_menu_edit_proses'])) {
    // Validate gambar
    $cek = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($cek === false) {
        echo "<script>alert('File yang diupload bukan gambar'); window.location.href='../menu';
}</script>";
        $statusUpload = 0;
    } else {
        $statusUpload = 1;
        if (file_exists($target_file)) {
            echo "<script>alert('File sudah ada'); window.location.href='../menu';</script>";
        } else {
            if ($_FILES["foto"]["size"] > 500000) {
                echo "<script>alert('File terlalu besar'); window.location.href='../menu';</script>";
                $statusUpload = 0;
            } elseif ($imageType != "jpg" && $imageType != "png" && $imageType != "jpeg" && $imageType != "gif") {
                echo "<script>alert('Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan'); window.location.href='../menu';</script>";
                $statusUpload = 0;
            }
        }
    }
    if ($statusUpload == 0) {
        echo "<script>alert('Gagal mengupload gambar'); window.location.href='../menu';</script>";
        exit();
    } else {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_menu WHERE nama = '$nama_menu'");
        if (mysqli_num_rows($select_query) > 0) {
            echo "<script>alert('Menu sudah terdaftar'); window.location.href='../menu';</script>";
            exit();
        } else {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $query = mysqli_query($conn, "UPDATE tb_menu SET nama = '$nama_menu', foto = '" . $kode_rand . $_FILES['foto']['name'] . "', keterangan = '$keterangan', kategori = '$kategori_menu', nama_toko = '$kios', harga = '$harga', stok = '$stok' WHERE id = '$_POST[id]'");
                if ($query) {
                    echo "<script>alert('Menu berhasil diupdate'); window.location.href='../menu';</script>";
                } else {
                    echo "<script>alert('Gagal mengupdate menu'); window.location.href='../menu';</script>";
                }
            } else {
                echo "<script>alert('Gagal mengupload gambar'); window.location.href='../menu';</script>";
            }
        }
    }
}
