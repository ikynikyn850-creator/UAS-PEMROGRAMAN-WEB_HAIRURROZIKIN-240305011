<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Ambil data kegiatan berdasarkan pengguna dan id
$stmt = mysqli_prepare($conn, "SELECT * FROM kegiatan WHERE id_kegiatan = ? AND id_user = ?"); // Mengambil data kegiatan dari database berdasarkan id_kegiatan dan id_user
$userId = intval($_SESSION['user']['id']); // Mendapatkan ID pengguna dari sesi
mysqli_stmt_bind_param($stmt, 'ii', $id, $userId); // Memasukkan ID kegiatan dan ID pengguna ke query
mysqli_stmt_execute($stmt);                        // Menjalankan query
$result = mysqli_stmt_get_result($stmt);           // Mengambil hasil query
$kegiatan = mysqli_fetch_assoc($result);           
if (!$kegiatan) {
    header('Location: dashboard.php');
    exit;

}
      // PROSES UPDATE DATA KEGIATAN.
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC"); //Mengambil data kategori 
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Mengecek form dikirim 
    $id_kategori = intval($_POST['id_kategori'] ?? 0); //Mengambil ID kategori
    $nama_kegiatan = trim($_POST['nama_kegiatan'] ?? ''); //
    $tanggal = trim($_POST['tanggal'] ?? '');
    $jam = trim($_POST['jam'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama_kegiatan === '' || $tanggal === '' || $jam === '') {
        $error = 'Nama kegiatan, tanggal, dan jam wajib diisi.';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE kegiatan SET id_kategori = ?, nama_kegiatan = ?, tanggal = ?, jam = ?, lokasi = ?, status = ?, keterangan = ? WHERE id_kegiatan = ? AND id_user = ?"); // Jika tidak kosong, maka query update akan dijalankan untuk memperbarui data kegiatan di database.
        mysqli_stmt_bind_param($stmt, 'issssssii', $id_kategori, $nama_kegiatan, $tanggal, $jam, $lokasi, $status, $keterangan, $id, $userId); // Memasukkan nilai-nilai ke query
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Kegiatan berhasil diperbarui.';
            $kegiatan = array_merge($kegiatan, [
                'id_kategori' => $id_kategori,
                'nama_kegiatan' => $nama_kegiatan,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'lokasi' => $lokasi,
                'status' => $status,
                'keterangan' => $keterangan
            ]);
        } else {
            $error = 'Gagal memperbarui kegiatan. Silakan coba lagi.'; // Jika query gagal dijalankan, sistem akan menyimpan pesan error ke variabel $error.
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Kegiatan - Kegiatan Sehari-hari</title>
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
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-arrow-left"></i> Kembali</a>
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
                        <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="bi bi-tags me-2"></i>Kategori</a></li>
                        <li class="nav-item"><a class="nav-link" href="laporan.php"><i class="bi bi-journal-text me-2"></i>Laporan</a></li>
                        <li class="nav-item"><a class="nav-link" href="upload_poto.php"><i class="bi bi-image me-2"></i>Upload Foto</a></li>
                    </ul>
                </div>
            </aside>

            <main class="content-area">
                <div class="pt-4 pb-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-2"><i class="bi bi-pencil-square me-2"></i>Edit Kegiatan</h1>
                        <p class="text-secondary mb-0">Perbarui data kegiatan sesuai kebutuhan </p>
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
                        <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i>Form Edit Kegiatan</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-tag me-2" style="color: #8B5CF6;"></i>Nama Kegiatan</label>
                                    <input type="text" name="nama_kegiatan" class="form-control" value="<?php echo htmlspecialchars($kegiatan['nama_kegiatan']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-tags me-2" style="color: #8B5CF6;"></i>Kategori</label>
                                    <select name="id_kategori" class="form-select">
                                        <option value="0">-- Pilih kategori --</option>
                                        <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                                            <option value="<?php echo $row['id_kategori']; ?>" <?php echo $kegiatan['id_kategori'] == $row['id_kategori'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['nama_kategori']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-calendar2 me-2" style="color: #8B5CF6;"></i>Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="<?php echo htmlspecialchars($kegiatan['tanggal']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-clock me-2" style="color: #8B5CF6;"></i>Jam</label>
                                    <input type="time" name="jam" class="form-control" value="<?php echo htmlspecialchars($kegiatan['jam']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-geo-alt me-2" style="color: #8B5CF6;"></i>Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" value="<?php echo htmlspecialchars($kegiatan['lokasi']); ?>" placeholder="Contoh: Ruang Meeting">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-circle-half me-2" style="color: #8B5CF6;"></i>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Terjadwal" <?php echo $kegiatan['status'] === 'Terjadwal' ? 'selected' : ''; ?>>⏳ Terjadwal</option>
                                        <option value="Selesai" <?php echo $kegiatan['status'] === 'Selesai' ? 'selected' : ''; ?>>✓ Selesai</option>
                                        <option value="Batal" <?php echo $kegiatan['status'] === 'Batal' ? 'selected' : ''; ?>>✗ Batal</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-pencil-fill me-2" style="color: #8B5CF6;"></i>Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="4" placeholder="Tambahkan catatan atau penjelasan..."><?php echo htmlspecialchars($kegiatan['keterangan']); ?></textarea>
                                </div>
                            </div>
                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-purple px-4 fw-600">
                                    <i class="bi bi-pencil-square me-2"></i>Perbarui Kegiatan
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary px-4 fw-600">
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
