<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

      //menghapus data laporan dari database berdasarkan ID laporan.
$id = intval($_GET['id'] ?? 0); // Mengambil ID laporan dari URL menggunakan $_GET['id'].
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM laporan WHERE id_laporan = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header('Location: laporan.php?success=' . urlencode('Laporan berhasil dihapus.')); // Mengarahkan pengguna kembali ke halaman laporan.php dengan pesan sukses.
exit;
