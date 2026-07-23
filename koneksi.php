<?php
// Koneksi Database
$host = "localhost"; //Menentukan alamat server database 
$user = "root";      //Menentukan username database
$pass = "";         //Menentukan password database
$db   = "kegiatan_harian";

$conn = mysqli_connect($host, $user, $pass, $db); //Membuat koneksi ke database.bagian utama untuk membuat koneksi antara PHP dan MySQL.

if (!$conn) { //$COON ADALAH variabel yang menyimpan koneksi antara PHP dengan database MySQL.
    die("Koneksi gagal: " . mysqli_connect_error()); // mengecek apakah koneksi database berhasil atau tidak.
}

mysqli_set_charset($conn, "utf8mb4");//Mengatur karakter database
?>
