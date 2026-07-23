<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

     // menghapus data kategori dari database berdasarkan ID kategori yang dipilih,
$id = intval($_GET['id'] ?? 0); // Mengambil ID kategori dari URL menggunakan $_GET['id'].
if ($id > 0) {                  // Mengecek apakah ID kategori valid, jika ID lebih besar dari 0, maka query DELETE akan dijalankan untuk menghapus data kategori dari database.
    $stmt = mysqli_prepare($conn, "DELETE FROM kategori WHERE id_kategori = ?"); // Membuat query DELETE untuk menghapus data kategori berdasarkan ID kategori.
    mysqli_stmt_bind_param($stmt, 'i', $id); // Memasukkan ID kategori ke query
    mysqli_stmt_execute($stmt); // Menjalankan query DELETE untuk menghapus data kategori dari database.
}

header('Location: kategori.php?success=' . urlencode('Kategori berhasil dihapus.'));
exit;
