<?php
session_start();

// Logout
$_SESSION = [];
session_unset(); // Menghapus semua variabel session
session_destroy(); // Menghancurkan session
header('Location: index.php'); // Mengarahkan pengguna kembali ke halaman login setelah logout
exit;
