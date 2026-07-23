<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id'] ?? 0); // Mengambil ID kategori untuk mengambil nilai id dari URL menggunakan $_GET['id'].
if ($id <= 0) { // mengecek apakah ID valid. Kalau ID kurang dari atau sama dengan 0, pengguna akan diarahkan kembali ke halaman kategori.
    header('Location: kategori.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM kategori WHERE id_kategori = ?"); //Mengambil data kategori dari database.
mysqli_stmt_bind_param($stmt, 'i', $id); // Memasukkan ID ke query
mysqli_stmt_execute($stmt);              // Menjalankan query
$result = mysqli_stmt_get_result($stmt); // mengambil hasil query
$kategori = mysqli_fetch_assoc($result); // mengambil satu baris data dan menyimpannya dalam bentuk array associative ke variabel $kategori.

if (!$kategori) { 
    header('Location: kategori.php'); // Mengecek apakah data ditemukan 
    exit;
}
 //Menyiapkan variabel pesan
$success = ''; //pesan ketika proses berhasil.
$error = ''; //pesan ketika proses gagal.

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //Mengecek apakah form dikirim
    $nama_kategori = trim($_POST['nama_kategori'] ?? ''); //Mengambil nilai nama kategori dari form dan menghapus spasi di awal dan akhir string.
    $deskripsi = trim($_POST['deskripsi'] ?? ''); // Mengambil nilai deskripsi dari form dan menghapus spasi di awal dan akhir string.

    if ($nama_kategori === '') { // Mengecek apakah nama kategori kosong
        $error = 'Nama kategori wajib diisi.'; // Jika kosong, maka pesan error akan ditampilkan.

         //. MEMPERBARUI DATA KATEGORI
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE kategori SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?"); // Jika tidak kosong, maka query update akan dijalankan untuk memperbarui data kategori di database.
        mysqli_stmt_bind_param($stmt, 'ssi', $nama_kategori, $deskripsi, $id);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Kategori berhasil diperbarui.';
            $kategori['nama_kategori'] = $nama_kategori;
            $kategori['deskripsi'] = $deskripsi;
        } else {
            $error = 'Gagal memperbarui kategori. Silakan coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">LAPORAN KEGIATAN</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="kategori.php" class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-arrow-left"></i> Kembali</a>
                <a href="logout.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <aside class="sidebar pt-5 pb-4">
                <div class="sidebar-sticky px-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="tambah_kegiatan.php"><i class="bi bi-plus-circle me-2"></i>Tambah Kegiatan</a></li>
                        <li class="nav-item"><a class="nav-link active" href="kategori.php"><i class="bi bi-tags me-2"></i>Kategori</a></li>
                        <li class="nav-item"><a class="nav-link" href="laporan.php"><i class="bi bi-journal-text me-2"></i>Laporan</a></li>
                        <li class="nav-item"><a class="nav-link" href="upload_poto.php"><i class="bi bi-image me-2"></i>Upload Foto</a></li>
                    </ul>
                </div>
            </aside>

            <main class="content-area">
                <div class="pt-4 pb-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-2"><i class="bi bi-pencil-square me-2"></i>Edit Kategori</h1>
                        <p class="text-secondary mb-0">Perbarui nama dan deskripsi kategori</p>
                    </div>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm rounded-4 border-0">
                    <div class="card-header bg-gradient text-white border-0 rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i>Form Edit Kategori</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-tag me-2" style="color: #8B5CF6;"></i>Nama Kategori</label>
                                    <input type="text" name="nama_kategori" class="form-control" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-chat-left-text me-2" style="color: #8B5CF6;"></i>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tambahkan deskripsi kategori..."><?php echo htmlspecialchars($kategori['deskripsi'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-purple px-4 fw-600">
                                    <i class="bi bi-pencil-square me-2"></i>Perbarui Kategori
                                </button>
                                <a href="kategori.php" class="btn btn-outline-secondary px-4 fw-600">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
