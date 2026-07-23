<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

    //menghapus data kegiatan tertentu yang dimiliki oleh user yang sedang login
$id = intval($_GET['id'] ?? 0);
$userId = intval($_SESSION['user']['id']);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM kegiatan WHERE id_kegiatan = ? AND id_user = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $id, $userId);
    mysqli_stmt_execute($stmt);
}

header('Location: dashboard.php');
exit;
