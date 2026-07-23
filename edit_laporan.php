<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$userId = intval($_SESSION['user']['id']);
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: laporan.php');
    exit;
}
 // Ambil data laporan berdasarkan pengguna dan id
$stmt = mysqli_prepare($conn, "SELECT l.*, k.nama_kegiatan FROM laporan l JOIN kegiatan k ON l.id_kegiatan = k.id_kegiatan WHERE l.id_laporan = ? AND k.id_user = ?"); //Membuat query untuk mengambil data 
mysqli_stmt_bind_param($stmt, 'ii', $id, $userId); //Membatasi laporan berdasarkan ID
mysqli_stmt_execute($stmt);                        //Memastikan laporan milik user
$result = mysqli_stmt_get_result($stmt); // Mengambil hasil query
$laporan = mysqli_fetch_assoc($result); // Mengambil satu baris data dan menyimpannya dalam bentuk array associative ke variabel $laporan.

if (!$laporan) {
    header('Location: laporan.php');
    exit;
}

      //FITUR EDIT LAPORAN
$kegiatan = mysqli_query($conn, "SELECT id_kegiatan, nama_kegiatan FROM kegiatan WHERE id_user = $userId ORDER BY nama_kegiatan ASC"); //Mengambil daftar kegiatan milik user
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Mengecek apakah form dikirim
    $id_kegiatan = intval($_POST['id_kegiatan'] ?? 0); // Mengambil ID kegiatan dari form
    $tanggal_laporan = trim($_POST['tanggal_laporan'] ?? ''); // Mengambil tanggal laporan dari form
    $catatan = trim($_POST['catatan'] ?? '');                 // Mengambil catatan laporan dari form
    $status_laporan = trim($_POST['status_laporan'] ?? '');   // Mengambil status laporan dari form

    if ($id_kegiatan <= 0 || $tanggal_laporan === '' || $catatan === '') { // Mengecek apakah ID kegiatan, tanggal laporan, atau catatan kosong
        $error = 'Pilih kegiatan, tanggal, dan catatan laporan terlebih dahulu.';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE laporan SET id_kegiatan = ?, tanggal_laporan = ?, catatan = ?, status_laporan = ? WHERE id_laporan = ?"); // Jika tidak kosong, maka query update akan dijalankan untuk memperbarui data laporan di database.
        mysqli_stmt_bind_param($stmt, 'isssi', $id_kegiatan, $tanggal_laporan, $catatan, $status_laporan, $id); // Memasukkan nilai-nilai ke query
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Laporan berhasil diperbarui.';
            $laporan['id_kegiatan'] = $id_kegiatan;
            $laporan['tanggal_laporan'] = $tanggal_laporan;
            $laporan['catatan'] = $catatan;
            $laporan['status_laporan'] = $status_laporan;
        } else {
            $error = 'Gagal memperbarui laporan. Silakan coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">LAPORAN KEGIATAN</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="laporan.php" class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-arrow-left"></i> Kembali</a>
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
                        <li class="nav-item"><a class="nav-link active" href="laporan.php"><i class="bi bi-journal-text me-2"></i>Laporan</a></li>
                        <li class="nav-item"><a class="nav-link" href="upload_poto.php"><i class="bi bi-image me-2"></i>Upload Foto</a></li>
                    </ul>
                </div>
            </aside>

            <main class="content-area">
                <div class="pt-4 pb-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-2"><i class="bi bi-pencil-square me-2"></i>Edit Laporan</h1>
                        <p class="text-secondary mb-0">Perbarui isi laporan kegiatan </p>
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
                        <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i>Form Edit Laporan</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-check2-square me-2" style="color: #8B5CF6;"></i>Pilih Kegiatan</label>
                                    <select name="id_kegiatan" class="form-select" required>
                                        <option value="">-- Pilih kegiatan --</option>
                                        <?php while ($row = mysqli_fetch_assoc($kegiatan)): ?>
                                            <option value="<?php echo $row['id_kegiatan']; ?>" <?php echo $laporan['id_kegiatan'] == $row['id_kegiatan'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['nama_kegiatan']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-calendar2-event me-2" style="color: #8B5CF6;"></i>Tanggal Laporan</label>
                                    <input type="date" name="tanggal_laporan" class="form-control" value="<?php echo htmlspecialchars($laporan['tanggal_laporan']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-circle-half me-2" style="color: #8B5CF6;"></i>Status Laporan</label>
                                    <select name="status_laporan" class="form-select">
                                        <option value="Dalam Proses" <?php echo $laporan['status_laporan'] === 'Dalam Proses' ? 'selected' : ''; ?>>⏳ Dalam Proses</option>
                                        <option value="Selesai" <?php echo $laporan['status_laporan'] === 'Selesai' ? 'selected' : ''; ?>>✓ Selesai</option>
                                        <option value="Tertunda" <?php echo $laporan['status_laporan'] === 'Tertunda' ? 'selected' : ''; ?>>⏸️ Tertunda</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-chat-left-text me-2" style="color: #8B5CF6;"></i>Catatan Laporan</label>
                                    <textarea name="catatan" class="form-control" rows="4" placeholder="Tuliskan catatan dan observasi lengkap..." required><?php echo htmlspecialchars($laporan['catatan']); ?></textarea>
                                </div>
                            </div>
                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-purple px-4 fw-600">
                                    <i class="bi bi-pencil-square me-2"></i>Perbarui Laporan
                                </button>
                                <a href="laporan.php" class="btn btn-outline-secondary px-4 fw-600">
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
