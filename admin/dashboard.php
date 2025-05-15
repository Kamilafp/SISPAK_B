<?php
session_start();
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../includes/header.php');

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
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
?>

<div class="container mt-5">
    <h2>Dashboard Admin</h2>
    
    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Penyakit</h5>
                    <h2 class="card-text"><?= $penyakit['total'] ?></h2>
                    <a href="penyakit.php" class="text-white">Lihat Detail</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Gejala</h5>
                    <h2 class="card-text"><?= $gejala['total'] ?></h2>
                    <a href="gejala.php" class="text-white">Lihat Detail</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Pengguna</h5>
                    <h2 class="card-text"><?= $user['total'] ?></h2>
                    <a href="#" class="text-white">Lihat Detail</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Riwayat</h5>
                    <h2 class="card-text"><?= $riwayat['total'] ?></h2>
                    <a href="#" class="text-dark">Lihat Detail</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Menu Admin</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="penyakit.php" class="btn btn-primary btn-block py-3">
                        <i class="fas fa-disease fa-2x mb-2"></i><br>
                        Kelola Penyakit
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="gejala.php" class="btn btn-success btn-block py-3">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i><br>
                        Kelola Gejala
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="aturan.php" class="btn btn-info btn-block py-3">
                        <i class="fas fa-project-diagram fa-2x mb-2"></i><br>
                        Kelola Aturan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/../includes/footer.php'); 
?>