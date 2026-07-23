<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$success = '';
$error = '';

// Proses Tambah Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = intval($_SESSION['user']['id']);
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $nama_kegiatan = trim($_POST['nama_kegiatan'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $jam = trim($_POST['jam'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama_kategori === '' || $nama_kegiatan === '' || $tanggal === '' || $jam === '') {
        $error = 'Kategori, nama kegiatan, tanggal, dan jam wajib diisi.';
    } else {
        // Cek apakah kategori sudah ada
        $stmtKat = mysqli_prepare($conn, "SELECT id_kategori FROM kategori WHERE nama_kategori = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtKat, 's', $nama_kategori);
        mysqli_stmt_execute($stmtKat);
        $resultKat = mysqli_stmt_get_result($stmtKat);

        if ($resultKat && $rowKat = mysqli_fetch_assoc($resultKat)) {
            $id_kategori = intval($rowKat['id_kategori']);
        } else {
            $stmtKat = mysqli_prepare($conn, "INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, '')");
            mysqli_stmt_bind_param($stmtKat, 's', $nama_kategori);
            mysqli_stmt_execute($stmtKat);
            $id_kategori = mysqli_insert_id($conn);
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO kegiatan (id_user, id_kategori, nama_kegiatan, tanggal, jam, lokasi, status, keterangan, dibuat_pada) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, 'iissssss', $id_user, $id_kategori, $nama_kegiatan, $tanggal, $jam, $lokasi, $status, $keterangan);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Kegiatan berhasil ditambahkan.';
        } else {
            $error = 'Gagal menyimpan kegiatan. Silakan coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kegiatan </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-fluid">
            <button class="btn btn-light d-md-none me-2" id="sidebarToggle"><i class="bi bi-list"></i></button>
            <a class="navbar-brand fw-bold" href="dashboard.php">LAPORAN KEGIATAN</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="UTS_PEMROGAMAN WEB_M. HAIRURROZIKIN 240305011/index.html" class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-arrow-left"></i> Kembali</a>
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
                        <li class="nav-item"><a class="nav-link active" href="tambah_kegiatan.php"><i class="bi bi-plus-circle me-2"></i>Tambah Kegiatan</a></li>
                        <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="bi bi-tags me-2"></i>Kategori</a></li>
                        <li class="nav-item"><a class="nav-link" href="laporan.php"><i class="bi bi-journal-text me-2"></i>Laporan</a></li>
                        <li class="nav-item"><a class="nav-link" href="upload_poto.php"><i class="bi bi-image me-2"></i>Upload Foto</a></li>
                    </ul>
                </div>
            </aside>

            <main class="content-area">
                <div class="pt-4 pb-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-2"><i class="bi bi-plus-circle me-2"></i>Tambah Kegiatan</h1>
                        <p class="text-secondary mb-0">Masukkan data kegiatan harian  dengan detail dan lengkap</p>
                    </div>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm rounded-4 border-0">
                    <div class="card-header bg-gradient text-white border-0 rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Kegiatan</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-tag me-2" style="color: #8B5CF6;"></i>Nama Kegiatan</label>
                                    <input type="text" name="nama_kegiatan" class="form-control" placeholder=>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-tags me-2" style="color: #8B5CF6;"></i>Kategori</label>
                                    <input type="text" name="nama_kategori" class="form-control" placeholder= >
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-calendar2 me-2" style="color: #8B5CF6;"></i>Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-clock me-2" style="color: #8B5CF6;"></i>Jam</label>
                                    <input type="time" name="jam" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-geo-alt me-2" style="color: #8B5CF6;"></i>Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" placeholder=>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-circle-half me-2" style="color: #8B5CF6;"></i>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Terjadwal">⏳ Terjadwal</option>
                                        <option value="Selesai">✓ Selesai</option>
                                        <option value="Batal">✗ Batal</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-pencil-fill me-2" style="color: #8B5CF6;"></i>Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="4" placeholder="Tambahkan catatan atau deskripsi detail kegiatan ..."></textarea>
                                </div>
                            </div>
                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-purple px-4 fw-600">
                                    <i class="bi bi-save me-2"></i>Simpan Kegiatan
                                </button>
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
