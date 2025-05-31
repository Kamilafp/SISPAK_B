<?php
require_once(__DIR__ . '/layout/header_layout.php');
$page_title = "Dashboard";

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

    <!-- Welcome Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card welcome-header-card">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?></h4>
                        <p><?= date('l, d F Y') ?></p>
                        <span class="role-badge">
                            <i class="fas fa-user-shield me-1"></i>
                            <?= htmlspecialchars(ucfirst($_SESSION['role'] ?? 'pengguna')) ?>
                        </span>
                    </div>
                    <div class="avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-disease"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Penyakit</h6>
                    <div class="stats-number text-dark"><?= $penyakit['total'] ?? 0 ?></div>
                    <?php if ($isPakar): ?>
                    <a href="penyakit/penyakit.php" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-start border-success border-4">
                <div class="card-body">
                    <div class="stats-icon text-success">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Gejala</h6>
                    <div class="stats-number text-dark"><?= $gejala['total'] ?? 0 ?></div>
                    <?php if ($isPakar): ?>
                    <a href="gejala/gejala.php" class="btn btn-sm btn-outline-success mt-2">
                        <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-start border-info border-4">
                <div class="card-body">
                    <div class="stats-icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Pengguna</h6>
                    <div class="stats-number text-dark"><?= $user['total'] ?? 0 ?></div>
                    <?php if ($isPakar): ?>
                    <a href="pengguna/pengguna.php" class="btn btn-sm btn-outline-info mt-2">
                        <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-history"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Riwayat</h6>
                    <div class="stats-number text-dark"><?= $riwayat['total'] ?? 0 ?></div>
                    <?php if ($isPakar): ?>
                    <a href="riwayat.php" class="btn btn-sm btn-outline-warning mt-2">
                        <i class="fas fa-arrow-right me-1"></i>Lihat Detail
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($isPakar): ?>
    <!-- Quick Actions hanya untuk pakar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                    </h5>
                    <span class="badge bg-warning text-dark">Pakar Mode</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <a href="penyakit/penyakit.php?action=add" class="quick-action-btn text-primary">
                                <i class="fas fa-plus-circle fa-2x"></i>
                                <span>Tambah Penyakit Baru</span>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <a href="gejala/gejala.php?action=add" class="quick-action-btn text-success">
                                <i class="fas fa-plus-circle fa-2x"></i>
                                <span>Tambah Gejala Baru</span>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <a href="aturan/aturan.php?action=add" class="quick-action-btn text-info">
                                <i class="fas fa-plus-circle fa-2x"></i>
                                <span>Tambah Aturan Baru</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="row">
        <!-- Recent Activity -->
        <?php if ($isAdmin): ?>
            <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>Jumlah Konsultasi per Hari (<?= date('F Y') ?>)
                </h5>
                </div>
                <div class="card-body">
                <?php
                // Ambil data jumlah konsultasi per hari pada bulan ini
                $year = date('Y');
                $month = date('m');
                $daysInMonth = date('t');
                $labels = [];
                $data = [];

                // Inisialisasi array hari dengan 0
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $labels[] = str_pad($d, 2, '0', STR_PAD_LEFT);
                    $data[$d] = 0;
                }

                $chart_query = "
                    SELECT DAY(tanggal) as hari, COUNT(*) as total 
                    FROM riwayat 
                    WHERE YEAR(tanggal) = '$year' AND MONTH(tanggal) = '$month'
                    GROUP BY hari
                    ORDER BY hari ASC
                ";
                $chart_result = mysqli_query($conn, $chart_query);

                while ($row = mysqli_fetch_assoc($chart_result)) {
                    $data[(int)$row['hari']] = (int)$row['total'];
                }

                // Siapkan data untuk chart.js
                $chart_data = array_values($data);
                ?>
                <canvas id="konsultasiChart" height="120"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                const ctx = document.getElementById('konsultasiChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Jumlah Konsultasi',
                        data: <?= json_encode($chart_data) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                    },
                    options: {
                    scales: {
                        x: { title: { display: true, text: 'Hari' } },
                        y: { beginAtZero: true, title: { display: true, text: 'Jumlah' } }
                    }
                    }
                });
                </script>
                </div>
            </div>
            </div>
        <?php else: ?>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2 text-primary"></i>Diagnosis Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($recent_result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Pengguna</th>
                                            <th>Hasil Diagnosis</th>
                                            <th>Tanggal</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($row['nama']) ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if(!empty($row['nama_penyakit'])): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-disease me-1"></i>
                                                        <?= htmlspecialchars($row['nama_penyakit']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-question me-1"></i>
                                                        Tidak Terdiagnosis
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="far fa-calendar me-1"></i>
                                                    <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="riwayat_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="riwayat.php" class="btn btn-primary">
                                    <i class="fas fa-eye me-1"></i>Lihat Semua Riwayat
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5 class="mt-3">Belum ada diagnosis</h5>
                                <p class="text-muted">Sistem belum mencatat diagnosis apapun</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- System Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-info"></i>Ringkasan Sistem
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Data Penyakit</span>
                            <span class="badge bg-primary"><?= $penyakit['total'] ?? 0 ?></span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: <?= min(100, ($penyakit['total'] ?? 0) * 10) ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Data Gejala</span>
                            <span class="badge bg-success"><?= $gejala['total'] ?? 0 ?></span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?= min(100, ($gejala['total'] ?? 0) * 5) ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Pengguna Aktif</span>
                            <span class="badge bg-info"><?= $user['total'] ?? 0 ?></span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: <?= min(100, ($user['total'] ?? 0) * 2) ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Diagnosis</span>
                            <span class="badge bg-warning text-dark"><?= $riwayat['total'] ?? 0 ?></span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: <?= min(100, ($riwayat['total'] ?? 0)) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
?>