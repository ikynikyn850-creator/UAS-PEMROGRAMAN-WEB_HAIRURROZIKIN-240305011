<?php //Menandakan bahwa kode yang ditulis adalah kode PHP.
session_start();//memulai sesion untuk menyimpan data pengguna yang login
require 'koneksi.php';//menghubungkan ke file koneksi.php untuk mengases database

if (!isset($_SESSION['user'])) {  //memeriksa apakah pengguna sudah login atau belum,
    header('Location: index.php'); // jika belum login, pengguna akan diarahkan ke halaman index.php
    exit;
}

$userId = intval($_SESSION['user']['id']); // Mendapatkan ID pengguna dari sesi
$userNama = htmlspecialchars($_SESSION['user']['nama']); // Mendapatkan nama pengguna dari sesi

// Hitung data untuk dashboard fungi
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM kegiatan WHERE id_user = $userId");
$kegiatan_count = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori");
$kategori_count = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM laporan l JOIN kegiatan k ON l.id_kegiatan = k.id_kegiatan WHERE k.id_user = $userId");
$laporan_count = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM galeri g JOIN kegiatan k ON g.id_kegiatan = k.id_kegiatan WHERE k.id_user = $userId");
$foto_count = mysqli_fetch_assoc($result)['total'];

// Ambil daftar kegiatan untuk ditampilkan
$recent = mysqli_query($conn, "SELECT k.*, c.nama_kategori FROM kegiatan k LEFT JOIN kategori c ON k.id_kategori = c.id_kategori WHERE k.id_user = $userId ORDER BY k.dibuat_pada DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
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
            <div class="collapse navbar-collapse">
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Halo, <?php echo $userNama; ?></span>
                    <a href="UTS_PEMROGAMAN WEB_M. HAIRURROZIKIN 240305011/index.html" class="btn btn-outline-secondary btn-sm me-1"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <a href="logout.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <aside class="sidebar pt-5 pb-4">
                <div class="sidebar-sticky px-3">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-3 text-uppercase">
                        <span>Menu Utama</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="tambah_kegiatan.php"><i class="bi bi-plus-circle me-2"></i>Tambah Kegiatan</a></li>
                        <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="bi bi-tags me-2"></i>Kategori</a></li>
                        <li class="nav-item"><a class="nav-link" href="laporan.php"><i class="bi bi-journal-text me-2"></i>Laporan</a></li>
                        <li class="nav-item"><a class="nav-link" href="upload_poto.php"><i class="bi bi-image me-2"></i>Upload Foto</a></li>
                    </ul>
                </div>
            </aside>

            <main class="content-area">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-4">
                    <div>
                        <h1 class="h2 fw-bold mb-2">Dashboard</h1>
                        <p class="text-secondary mb-0"><i class="bi bi-calendar3 me-2"></i>Ringkasan kegiatan harian </p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm rounded-4 border-0 card-dashboard h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title text-secondary small mb-2">Total Kegiatan</h6>
                                        <div class="display-6 fw-bold" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php echo $kegiatan_count; ?></div>
                                    </div>
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(109, 40, 217, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-check2-square text-primary" style="font-size: 1.8rem; color: #8B5CF6;"></i>
                                    </div>
                                </div>
                                <div class="text-muted small">Semua kegiatan </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm rounded-4 border-0 card-dashboard h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title text-secondary small mb-2">Kategori</h6>
                                        <div class="display-6 fw-bold" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php echo $kategori_count; ?></div>
                                    </div>
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(109, 40, 217, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-tags text-primary" style="font-size: 1.8rem; color: #8B5CF6;"></i>
                                    </div>
                                </div>
                                <div class="text-muted small">Jumlah kategori tersedia</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm rounded-4 border-0 card-dashboard h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title text-secondary small mb-2">Laporan</h6>
                                        <div class="display-6 fw-bold" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php echo $laporan_count; ?></div>
                                    </div>
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(109, 40, 217, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-journal-text text-primary" style="font-size: 1.8rem; color: #8B5CF6;"></i>
                                    </div>
                                </div>
                                <div class="text-muted small">Catatan aktivitas harian</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm rounded-4 border-0 card-dashboard h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title text-secondary small mb-2">Foto</h6>
                                        <div class="display-6 fw-bold" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php echo $foto_count; ?></div>
                                    </div>
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(109, 40, 217, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-image text-primary" style="font-size: 1.8rem; color: #8B5CF6;"></i>
                                    </div>
                                </div>
                                <div class="text-muted small">Foto kegiatan yang diunggah</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm rounded-4 border-0 mb-4">
                    <div class="card-header bg-gradient text-white border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Kegiatan</h5>
                        <a href="tambah_kegiatan.php" class="btn btn-light btn-sm"><i class="bi bi-plus-circle me-1"></i>Tambah Kegiatan</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-purple text-white">
                                    <tr>
                                        <th>Nama Kegiatan</th>
                                        <th>Kategori</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody> 

                                
                                    <?php if (mysqli_num_rows($recent) > 0): ?> 
                                        <?php while ($row = mysqli_fetch_assoc($recent)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['nama_kegiatan']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_kategori'] ?: '-'); ?></td>
                                                <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                                <td><?php echo htmlspecialchars($row['jam']); ?></td>
                                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                <td>
                                                    <a href="edit_kegiatan.php?id=<?php echo $row['id_kegiatan']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a> 
                                                    <a href="hapus_kegiatan.php?id=<?php echo $row['id_kegiatan']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus kegiatan ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center text-muted">Belum ada kegiatan untuk ditampilkan.</td></tr>
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
