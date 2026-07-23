<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$userId = intval($_SESSION['user']['id']);
$success = '';
$error = '';

     //kode proses upload foto kegiatan.
$kegiatan = mysqli_query($conn, "SELECT id_kegiatan, nama_kegiatan FROM kegiatan WHERE id_user = $userId ORDER BY nama_kegiatan ASC"); // Mengambil daftar kegiatan yang dimiliki oleh user yang sedang login dari tabel kegiatan berdasarkan ID user dan mengurutkannya secara ascending berdasarkan nama kegiatan.

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Mengecek apakah metode request adalah POST, yang berarti form upload foto telah dikirimkan.
    $id_kegiatan = intval($_POST['id_kegiatan'] ?? 0); // Mengambil ID kegiatan dari form dan mengubahnya menjadi integer.
    $deskripsi = trim($_POST['deskripsi'] ?? ''); // Mengambil deskripsi foto dari form dan menghapus spasi di awal dan akhir string.

    if ($id_kegiatan <= 0) { 
        $error = 'Pilih kegiatan terlebih dahulu.';
    } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Pilih file foto terlebih dahulu.';
    } else {
        $file = $_FILES['foto'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            $error = 'Format file harus JPG, PNG, atau WEBP.';
        } else {
            $folder = 'uploads/poto/';
            $filename = uniqid('img_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $filepath = $folder . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO galeri (id_kegiatan, nama_file, deskripsi, tanggal_uploud) VALUES (?, ?, ?, NOW())");
                mysqli_stmt_bind_param($stmt, 'iss', $id_kegiatan, $filename, $deskripsi);
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Foto berhasil diunggah.';
                } else {
                    $error = 'Gagal menyimpan data galeri.';
                }
            } else {
                $error = 'Gagal memindahkan file ke folder uploads.';
            }
        }
    }
}

$foto = mysqli_query($conn, "SELECT g.*, g.tanggal_uploud AS tanggal_upload, k.nama_kegiatan FROM galeri g JOIN kegiatan k ON g.id_kegiatan = k.id_kegiatan WHERE k.id_user = $userId ORDER BY g.tanggal_uploud DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Foto</title>
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
                        <li class="nav-item"><a class="nav-link" href="tambah_kegiatan.php"><i class="bi bi-plus-circle me-2"></i>Tambah Kegiatan</a></li>
                        <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="bi bi-tags me-2"></i>Kategori</a></li>
                        <li class="nav-item"><a class="nav-link" href="laporan.php"><i class="bi bi-journal-text me-2"></i>Laporan</a></li>
                        <li class="nav-item"><a class="nav-link active" href="upload_poto.php"><i class="bi bi-image me-2"></i>Upload Foto</a></li>
                    </ul>
                </div>
            </aside>

            <main class="content-area">
                <div class="pt-4 pb-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-2"><i class="bi bi-image me-2"></i>Galeri Foto Kegiatan</h1>
                        <p class="text-secondary mb-0">Unggah dan kelola foto kegiatan dengan mudah</p>
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

                <div class="card shadow-sm rounded-4 border-0 mb-4">
                    <div class="card-header bg-gradient text-white border-0 rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Unggah Foto Baru</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-check2-square me-2" style="color: #8B5CF6;"></i>Pilih Kegiatan</label>
                                    <select name="id_kegiatan" class="form-select" required>
                                        <option value="">-- Pilih kegiatan --</option>
                                        <?php while ($row = mysqli_fetch_assoc($kegiatan)): ?>
                                            <option value="<?php echo $row['id_kegiatan']; ?>"><?php echo htmlspecialchars($row['nama_kegiatan']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-card-image me-2" style="color: #8B5CF6;"></i>Pilih Foto</label>
                                    <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                                    <small class="text-muted d-block mt-2">Format: JPG, PNG, atau WEBP (Max 5MB)</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-chat-left-text me-2" style="color: #8B5CF6;"></i>Deskripsi Foto</label>
                                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tambahkan deskripsi foto Anda..."></textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-purple px-4 fw-600">
                                    <i class="bi bi-cloud-upload me-2"></i>Unggah Foto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <h5 class="fw-bold mb-3"><i class="bi bi-images me-2"></i>Galeri Foto</h5>
                    <div class="row g-3">
                        <?php if (mysqli_num_rows($foto) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($foto)): ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="card shadow-sm rounded-4 border-0 h-100 overflow-hidden">
                                        <div style="height: 220px; overflow: hidden; position: relative; background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(139, 92, 246, 0.05));">
                                            <img src="uploads/poto/<?php echo htmlspecialchars($row['nama_file']); ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;" alt="Foto kegiatan">
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title mb-2 fw-bold text-truncate" title="<?php echo htmlspecialchars($row['nama_kegiatan']); ?>">
                                                <i class="bi bi-tag me-2" style="color: #8B5CF6;"></i><?php echo htmlspecialchars($row['nama_kegiatan']); ?>
                                            </h6>
                                            <p class="card-text text-muted small mb-2 text-truncate">
                                                <?php echo !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : 'Tanpa deskripsi'; ?>
                                            </p>
                                            <p class="small text-secondary mb-0">
                                                <i class="bi bi-calendar me-1"></i><?php echo date('d M Y', strtotime($row['tanggal_upload'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center py-5" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(139, 92, 246, 0.05)); border-color: rgba(139, 92, 246, 0.2);">
                                    <i class="bi bi-image" style="font-size: 3rem; color: #8B5CF6; display: block; margin-bottom: 1rem;"></i>
                                    <strong>Galeri Kosong</strong>
                                    <p class="text-muted mt-2 mb-0">Mulai unggah foto kegiatan Anda untuk mengisi galeri ini</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
