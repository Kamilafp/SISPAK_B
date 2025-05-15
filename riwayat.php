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

<div class="container mt-5">
    <?php if ($detail_id && $detail_row): ?>
        <!-- TAMPILAN DETAIL -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detail Konsultasi</h4>
                    <a href="riwayat.php" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informasi Konsultasi</h5>
                        <table class="table table-sm">
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
                    <div class="col-md-6">
                        <h5>Gejala yang Dipilih</h5>
                        <div class="alert alert-secondary">
                            <?= !empty($detail_row['gejala']) ? nl2br(htmlspecialchars($detail_row['gejala'])) : 'Tidak ada data gejala tersimpan' ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5>Deskripsi Penyakit</h5>
                        <div class="card card-body bg-light">
                            <?= nl2br(htmlspecialchars($detail_row['deskripsi'])) ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5>Solusi Penanganan</h5>
                        <div class="card card-body bg-light">
                            <?= nl2br(htmlspecialchars($detail_row['solusi'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- TAMPILAN DAFTAR RIWAYAT -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Riwayat Konsultasi Saya</h4>
                    <a href="dashboard.php" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (mysqli_num_rows($riwayat_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
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
                                        </td></td>
                                        <td>
                                            <a href="riwayat.php?detail=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">Sebelumnya</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Selanjutnya</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Anda belum memiliki riwayat konsultasi.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>