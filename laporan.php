<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

  //mengambil ID user yang sedang login dan mengatur pesan notifikasi sukses atau error yang dikirim melalui URL.
$userId = intval($_SESSION['user']['id']); // Mengambil ID user dari session yang sedang login dan mengubahnya menjadi integer.
$error = '';
$success = '';
$successMessage = trim($_GET['success'] ?? ''); // Mengambil pesan sukses dari URL menggunakan $_GET['success'] dan menghapus spasi di awal dan akhir string.
$errorMessage = trim($_GET['error'] ?? '');     // Mengambil pesan error dari URL menggunakan $_GET['error'] dan menghapus spasi di awal dan akhir string.
if ($successMessage !== '') {
    $success = $successMessage;
}
if ($errorMessage !== '') {
    $error = $errorMessage;
}

// Proses tambah laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Mengecek apakah metode request adalah POST, yang berarti form laporan baru telah dikirimkan.
    $id_kegiatan = intval($_POST['id_kegiatan'] ?? 0); // Mengambil ID kegiatan dari form dan mengubahnya menjadi integer.
    $tanggal_laporan = trim($_POST['tanggal_laporan'] ?? ''); // Mengambil tanggal laporan dari form dan menghapus spasi di awal dan akhir string.
    $catatan = trim($_POST['catatan'] ?? ''); // Mengambil catatan dari form dan menghapus spasi di awal dan akhir string.
    $status_laporan = trim($_POST['status_laporan'] ?? ''); // Mengambil status laporan dari form dan menghapus spasi di awal dan akhir string.

    if ($id_kegiatan <= 0 || $tanggal_laporan === '') {
        $error = 'Pilih kegiatan dan tanggal laporan terlebih dahulu.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO laporan (id_kegiatan, tanggal_laporan, catatan, status_laporan) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'isss', $id_kegiatan, $tanggal_laporan, $catatan, $status_laporan);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Laporan berhasil ditambahkan.';
        } else {
            $error = 'Gagal menyimpan laporan. Silakan coba lagi.';
        }
    }
}

$kegiatan = mysqli_query($conn, "SELECT id_kegiatan, nama_kegiatan FROM kegiatan WHERE id_user = $userId ORDER BY nama_kegiatan ASC");
$laporan = mysqli_query($conn, "SELECT l.*, k.nama_kegiatan FROM laporan l JOIN kegiatan k ON l.id_kegiatan = k.id_kegiatan WHERE k.id_user = $userId ORDER BY l.tanggal_laporan DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan</title>
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
                        <li class="nav-item"><a class="nav-link active" href="laporan.php"><i class="bi bi-journal-text me-2"></i>Laporan</a></li>
                        <li class="nav-item"><a class="nav-link" href="upload_poto.php"><i class="bi bi-image me-2"></i>Upload Foto</a></li>
                        
                    </ul>
                </div>
            </aside>

            <main class="content-area">
                <div class="pt-4 pb-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-2"><i class="bi bi-journal-text me-2"></i>Laporan Kegiatan</h1>
                        <p class="text-secondary mb-0">Catat kemajuan dan perkembangan kegiatan harian </p>
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
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Tambah Laporan Baru</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
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
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-calendar2-event me-2" style="color: #8B5CF6;"></i>Tanggal Laporan</label>
                                    <input type="date" name="tanggal_laporan" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-circle-half me-2" style="color: #8B5CF6;"></i>Status Laporan</label>
                                    <select name="status_laporan" class="form-select">
                                        <option value="Dalam Proses">⏳ Dalam Proses</option>
                                        <option value="Selesai">✓ Selesai</option>
                                        <option value="Tertunda">⏸️ Tertunda</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600 mb-2"><i class="bi bi-chat-left-text me-2" style="color: #8B5CF6;"></i>Catatan Laporan</label>
                                    <textarea name="catatan" class="form-control" rows="4" placeholder="Tuliskan catatan dan observasi lengkap dari kegiatan Anda..." required></textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-purple px-4 fw-600">
                                    <i class="bi bi-plus-circle me-2"></i>Tambahkan Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm rounded-4 border-0">
                    <div class="card-header bg-gradient text-white border-0 rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Daftar Laporan Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-purple text-white">
                                    <tr>
                                        <th>Kegiatan</th>
                                        <th style="width: 130px;">Tanggal</th>
                                        <th style="width: 120px;">Status</th>
                                        <th>Catatan</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($laporan) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($laporan)): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($row['nama_kegiatan']); ?></strong></td>
                                                <td><?php echo date('d M Y', strtotime($row['tanggal_laporan'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $status = $row['status_laporan'];
                                                    if ($status === 'Selesai') {
                                                        echo '<span class="badge" style="background: #22c55e;">✓ Selesai</span>';
                                                    } elseif ($status === 'Tertunda') {
                                                        echo '<span class="badge" style="background: #f59e0b;">⏸️ Tertunda</span>';
                                                    } else {
                                                        echo '<span class="badge" style="background: #8B5CF6;">⏳ Proses</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><span class="text-muted text-truncate d-inline-block" style="max-width: 300px; "><?php echo htmlspecialchars($row['catatan']); ?></span></td>
                                                <td>
                                                    <a href="edit_laporan.php?id=<?php echo $row['id_laporan']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="hapus_laporan.php?id=<?php echo $row['id_laporan']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus laporan ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>Belum ada laporan.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
