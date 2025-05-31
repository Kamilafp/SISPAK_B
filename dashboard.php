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
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fa;
    }
    
    .hero-section {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        padding: 3rem 0;
        border-radius: 0 0 20px 20px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
        margin-bottom: 20px;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .card-header {
        border-radius: 15px 15px 0 0 !important;
        font-weight: 600;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        border-radius: 50px;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .list-group-item {
        border: none;
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 10px !important;
        transition: all 0.3s;
    }
    
    .list-group-item:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateX(5px);
    }
    
    .table th {
        border-top: none;
        color: var(--primary-color);
    }
    
    .welcome-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08);
    }
    
    .welcome-title {
        color: var(--dark-color);
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .welcome-text {
        color: var(--dark-color);
        margin-bottom: 2rem;
    }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="welcome-card">
                <h2 class="welcome-title">Selamat datang, <?= $_SESSION['nama'] ?>!</h2>
                <p class="welcome-text">Sistem pakar ini akan membantu Anda mendiagnosa penyakit gigi berdasarkan gejala yang Anda alami.</p>
                <a href="konsultasi.php" class="btn btn-primary">Mulai Diagnosa Sekarang</a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white" style="background-color: var(--primary-color);">
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
                <div class="card-header text-white" style="background-color: var(--secondary-color);">
                    <h4>Menu Cepat</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="konsultasi.php" class="list-group-item list-group-item-action">Diagnosa Penyakit</a>
                        <a href="riwayat.php" class="list-group-item list-group-item-action">Lihat Riwayat Lengkap</a>
                        <a href="informasi_penyakit.php" class="list-group-item list-group-item-action">Informasi Penyakit Gigi</a>
                        <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>