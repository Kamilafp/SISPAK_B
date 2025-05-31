<?php
$page_title = "Dashboard";
require_once(__DIR__ . '/layout/header_layout.php');

if (!isLoggedIn()) {
    header('Location: /../../login.php');
    exit();
}

// Hitung jumlah data
$penyakit_query = "SELECT COUNT(*) as total FROM penyakit";
$penyakit_result = mysqli_query($conn, $penyakit_query);
$penyakit = mysqli_fetch_assoc($penyakit_result);

$gejala_query = "SELECT COUNT(*) as total FROM gejala";
$gejala_result = mysqli_query($conn, $gejala_query);
$gejala = mysqli_fetch_assoc($gejala_result);

$user_query = "SELECT COUNT(*) as total FROM users";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

$riwayat_query = "SELECT COUNT(*) as total FROM riwayat";
$riwayat_result = mysqli_query($conn, $riwayat_query);
$riwayat = mysqli_fetch_assoc($riwayat_result);

// Query untuk diagnosis terbaru
$recent_query = "SELECT r.*, u.nama, p.nama_penyakit 
                FROM riwayat r
                JOIN users u ON r.user_id = u.id
                LEFT JOIN penyakit p ON r.penyakit_id = p.id
                ORDER BY r.tanggal DESC
                LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$isPakar = ($_SESSION['role'] ?? '') === 'pakar';
?>

<div class="container-fluid py-4">
    <!-- Flash Message -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash_message']['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_message']); endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php if ($isPakar): ?>
            <!-- Tampilkan semua card untuk pakar -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card bg-primary text-white">
                    <div class="card-body position-relative">
                        <div class="stats-icon">
                            <i class="fas fa-disease"></i>
                        </div>
                        <h6 class="card-title">Total Penyakit</h6>
                        <div class="stats-number"><?= $penyakit['total'] ?? 0 ?></div>
                        <a href="penyakit/penyakit.php" class="btn btn-light btn-sm mt-2">
                            <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card bg-success text-white">
                    <div class="card-body position-relative">
                        <div class="stats-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h6 class="card-title">Total Gejala</h6>
                        <div class="stats-number"><?= $gejala['total'] ?? 0 ?></div>
                        <a href="gejala/gejala.php" class="btn btn-light btn-sm mt-2">
                            <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card bg-info text-white">
                <div class="card-body position-relative">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="card-title">Total Pengguna</h6>
                    <div class="stats-number"><?= $user['total'] ?? 0 ?></div>
                    <a href="pengguna/pengguna.php" class="btn btn-light btn-sm mt-2">
                        <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($isPakar): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card bg-warning text-dark">
                    <div class="card-body position-relative">
                        <div class="stats-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h6 class="card-title">Total Riwayat</h6>
                        <div class="stats-number"><?= $riwayat['total'] ?? 0 ?></div>
                        <a href="riwayat.php" class="btn btn-dark btn-sm mt-2">
                            <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isPakar): ?>
    <!-- Quick Actions hanya untuk pakar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <a href="penyakit/penyakit.php?action=add" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                Tambah Penyakit Baru
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <a href="gejala/gejala.php?action=add" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                Tambah Gejala Baru
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <a href="aturan/aturan.php?action=add" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                Tambah Aturan Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Activity -->
    <div class="row">
        <?php if ($isPakar): ?>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Diagnosis Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($recent_result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Pengguna</th>
                                        <th>Hasil Diagnosis</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-user me-2"></i>
                                            <?= htmlspecialchars($row['nama']) ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($row['nama_penyakit'])): ?>
                                                <span class="badge bg-success">
                                                    <?= htmlspecialchars($row['nama_penyakit']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak Terdiagnosis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="riwayat.php" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>Lihat Semua Riwayat
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada diagnosis yang dilakukan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="<?= $isPakar ? 'col-lg-4' : 'col-lg-12' ?>">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Sistem
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($isPakar): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Data Penyakit</span>
                                <span class="badge bg-primary"><?= $penyakit['total'] ?? 0 ?></span>
                            </div>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Data Gejala</span>
                                <span class="badge bg-success"><?= $gejala['total'] ?? 0 ?></span>
                            </div>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-success" style="width: 85%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Pengguna Aktif</span>
                            <span class="badge bg-info"><?= $user['total'] ?? 0 ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar bg-info" style="width: 65%"></div>
                        </div>
                    </div>
                    
                    <?php if ($isPakar): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total Diagnosis</span>
                                <span class="badge bg-warning text-dark"><?= $riwayat['total'] ?? 0 ?></span>
                            </div>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-warning" style="width: 45%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
?>