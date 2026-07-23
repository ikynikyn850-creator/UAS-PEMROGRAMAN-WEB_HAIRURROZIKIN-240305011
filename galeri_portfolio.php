<?php
session_start();
require 'koneksi.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Pengguna belum login.']);
    exit;
}

$userId = intval($_SESSION['user']['id']);
$stmt = mysqli_prepare($conn, "SELECT g.nama_file, g.deskripsi, g.tanggal_uploud, k.nama_kegiatan FROM galeri g JOIN kegiatan k ON g.id_kegiatan = k.id_kegiatan WHERE k.id_user = ? ORDER BY g.tanggal_uploud DESC");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$galeri = [];
while ($row = mysqli_fetch_assoc($result)) {
    $galeri[] = [
        'nama_file' => basename($row['nama_file']),
        'deskripsi' => $row['deskripsi'] ?? '',
        'tanggal_upload' => $row['tanggal_uploud'],
        'nama_kegiatan' => $row['nama_kegiatan']
    ];
}

echo json_encode($galeri, JSON_UNESCAPED_UNICODE);
