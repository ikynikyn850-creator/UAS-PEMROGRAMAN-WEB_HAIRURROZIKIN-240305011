<?php
session_start();
require 'koneksi.php';

// Proses Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Mengecek apakah metode request adalah POST, yang berarti form login telah dikirimkan.
    $username = trim($_POST['username'] ?? ''); // Mengambil username
    $password = trim($_POST['password'] ?? ''); // Mengambil password 

    if ($username === '' || $password === '') {
        header('Location: index.php?error=' . urlencode('Username dan password wajib diisi')); // Mengecek apakah username dan password kosong, jika kosong maka akan diarahkan kembali ke halaman login dengan pesan error.
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?"); // Membuat query SELECT untuk mengambil data user berdasarkan username yang dimasukkan.
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $hash = $row['password'];
        if ($password === $hash || password_verify($password, $hash)) {
            $_SESSION['user'] = [
                'id' => $row['id_users'],
                'nama' => $row['nama_lengkap'],
                'username' => $row['username'],
                'role' => $row['role']
            ];
            header('Location: UTS_PEMROGAMAN WEB_M. HAIRURROZIKIN 240305011/index.html');
            exit;
        }
    }

    header('Location: index.php?error=' . urlencode('Username atau password salah'));
    exit;
}

header('Location: index.php');
exit;
