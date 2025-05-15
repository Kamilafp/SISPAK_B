<?php
session_start();
require_once(__DIR__ . '/includes/functions.php');
require_once(__DIR__ . '/includes/header.php');


if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Ambil riwayat diagnosa user
$user_id = $_SESSION['user_id'];
$riwayat_query = "SELECT r.*, p.nama_penyakit 
                 FROM riwayat r 
                 JOIN penyakit p ON r.penyakit_id = p.id 
                 WHERE r.user_id = $user_id 
                 ORDER BY r.tanggal DESC 
                 LIMIT 5";
$riwayat_result = mysqli_query($conn, $riwayat_query);
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Selamat datang, <?= $_SESSION['nama'] ?>!</h2>
                    <p class="card-text">Sistem pakar ini akan membantu Anda mendiagnosa penyakit gigi berdasarkan gejala yang Anda alami.</p>
                    <a href="konsultasi.php" class="btn btn-primary">Mulai Diagnosa Sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4>Riwayat Diagnosa Terakhir</h4>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($riwayat_result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Penyakit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($riwayat_result)): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= $row['nama_penyakit'] ?></td>
                                        <td>
                                            <a href="riwayat.php?detail=<?= $row['id'] ?>" class="btn btn-sm btn-info">Lihat Detail</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Anda belum melakukan diagnosa.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4>Menu Cepat</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="konsultasi.php" class="list-group-item list-group-item-action">Diagnosa Penyakit</a>
                        <a href="riwayat.php" class="list-group-item list-group-item-action">Lihat Riwayat Lengkap</a>
                        <a href="#" class="list-group-item list-group-item-action">Informasi Penyakit Gigi</a>
                        <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>