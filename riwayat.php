<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Pagination config
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Parameter detail
$detail_id = isset($_GET['detail']) ? (int)$_GET['detail'] : null;

// Ambil data riwayat
$user_id = $_SESSION['user_id'];

if ($detail_id) {
    // Mode tampilan detail
    $query = "SELECT r.*, p.nama_penyakit, p.deskripsi, p.solusi 
              FROM riwayat r 
              JOIN penyakit p ON r.penyakit_id = p.id 
              WHERE r.id = ? AND r.user_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $detail_id, $user_id);
    mysqli_stmt_execute($stmt);
    
    $detail_result = mysqli_stmt_get_result($stmt);
    $detail_row = mysqli_fetch_assoc($detail_result);
} else {
    // Mode daftar riwayat
    $query = "SELECT r.*, p.nama_penyakit 
              FROM riwayat r 
              JOIN penyakit p ON r.penyakit_id = p.id 
              WHERE r.user_id = ? 
              ORDER BY r.id DESC 
              LIMIT ?, ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $offset, $per_page);
    mysqli_stmt_execute($stmt);

    $riwayat_result = mysqli_stmt_get_result($stmt);

    // Hitung total records untuk pagination
    $total_query = "SELECT COUNT(*) as total FROM riwayat WHERE user_id = ?";
    $stmt_total = mysqli_prepare($conn, $total_query);
    mysqli_stmt_bind_param($stmt_total, "i", $user_id);
    mysqli_stmt_execute($stmt_total);

    $total_result = mysqli_stmt_get_result($stmt_total);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $per_page);
}
?>

<style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #2980b9;
        --accent-color: #e74c3c;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --gradient-start: #3498db;
        --gradient-end: #2c3e50;
    }
    
    .history-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .history-header {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
    }
    
    .btn-back {
        background-color: white;
        color: var(--primary-color);
        border-radius: 50px;
        padding: 5px 15px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-back:hover {
        background-color: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }
    
    .table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    
    .table thead th {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }
    
    .table tbody tr {
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }
    
    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .table tbody td {
        vertical-align: middle;
        border-top: none;
        padding: 1rem;
    }
    
    .btn-detail {
        background-color: var(--primary-color);
        color: white;
        border-radius: 50px;
        padding: 5px 15px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-detail:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        color: white;
    }
    
    .info-card {
        border-radius: 10px;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s;
    }
    
    .info-card:hover {
        transform: translateX(5px);
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .pagination .page-link {
        color: var(--primary-color);
        margin: 0 5px;
        border-radius: 50px !important;
    }
    
    .pagination .page-link:hover {
        color: var(--secondary-color);
    }
    
    .alert-info {
        background-color: rgba(52, 152, 219, 0.1);
        border-color: rgba(52, 152, 219, 0.2);
        color: var(--secondary-color);
    }
</style>

<div class="container mt-4">
    <?php if ($detail_id && $detail_row): ?>
        <!-- TAMPILAN DETAIL -->
        <div class="history-card">
            <div class="history-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-alt me-2"  style="margin-right: 0.5rem !important;"></i> Detail Konsultasi</h4>
                    <a href="riwayat.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="info-card card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><i class="fas fa-info-circle me-2"  style="margin-right: 0.5rem !important;"></i>Informasi Konsultasi</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="30%">Tanggal</th>
                                        <td><?= date('d F Y', strtotime($detail_row['tanggal'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Penyakit</th>
                                        <td><?= htmlspecialchars($detail_row['nama_penyakit']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="info-card card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><i class="fas fa-clipboard-list me-2"  style="margin-right: 0.5rem !important;"></i>Gejala yang Dipilih</h5>
                                <div class="alert alert-secondary p-3">
                                    <?= !empty($detail_row['gejala']) ? nl2br(htmlspecialchars($detail_row['gejala'])) : 'Tidak ada data gejala tersimpan' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="info-card card">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><i class="fas fa-book me-2"  style="margin-right: 0.5rem !important;"></i>Deskripsi Penyakit</h5>
                                <div class="card-text p-3 bg-light rounded">
                                    <?= nl2br(htmlspecialchars($detail_row['deskripsi'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-card card">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><i class="fas fa-lightbulb me-2"  style="margin-right: 0.5rem !important;"></i>Solusi Penanganan</h5>
                                <div class="card-text p-3 bg-light rounded">
                                    <?= nl2br(htmlspecialchars($detail_row['solusi'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- TAMPILAN DAFTAR RIWAYAT -->
        <div class="history-card">
            <div class="history-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i> Riwayat Konsultasi Saya</h4>
                    <a href="dashboard.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (mysqli_num_rows($riwayat_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="15%">Tanggal</th>
                                    <th width="25%">Penyakit</th>
                                    <th width="40%">Gejala Dipilih</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($riwayat_result)): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= htmlspecialchars($row['nama_penyakit']) ?></td>
                                        <td>
                                            <?php 
                                            if (!empty($row['gejala'])) {
                                                $gejala_list = explode(',', $row['gejala']);
                                                $display_gejala = array_slice($gejala_list, 0, 3);
                                                echo htmlspecialchars(implode(', ', $display_gejala));
                                                if (count($gejala_list) > 3) {
                                                    echo '...';
                                                }
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="riwayat.php?detail=<?= $row['id'] ?>" class="btn btn-detail">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">
                                        <i class="fas fa-chevron-left"></i> Sebelumnya
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">
                                        Selanjutnya <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php else: ?>
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>Anda belum memiliki riwayat konsultasi</h5>
                        <p class="mb-0">Mulai konsultasi sekarang untuk melihat riwayat di sini</p>
                        <a href="konsultasi.php" class="btn btn-primary mt-3">
                            <i class="fas fa-stethoscope me-2"></i> Mulai Konsultasi
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>