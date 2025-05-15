<?php
session_start();
require_once(__DIR__ . '/includes/functions.php');
require_once(__DIR__ . '/includes/header.php');

?>

<div class="container mt-5">
    <div class="jumbotron text-center">
        <h1 class="display-4">Sistem Pakar Diagnosa Penyakit Gigi</h1>
        <p class="lead">Sistem ini membantu Anda mendiagnosa penyakit gigi secara mandiri menggunakan metode Naïve Bayes</p>
        <hr class="my-4">
        <p>Pilih menu dibawah untuk memulai</p>
        <div class="mt-4">
            <a href="register.php" class="btn btn-primary btn-lg mr-3">Daftar</a>
            <a href="login.php" class="btn btn-success btn-lg">Masuk</a>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Diagnosa Mandiri</h5>
                    <p class="card-text">Lakukan diagnosa penyakit gigi berdasarkan gejala yang Anda alami.</p>
                    <a href="login.php" class="btn btn-primary">Mulai Diagnosa</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informasi Penyakit</h5>
                    <p class="card-text">Pelajari berbagai jenis penyakit gigi dan gejalanya.</p>
                    <a href="sispak/admin/penyakit" class="btn btn-primary">Lihat Daftar</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tentang Sistem</h5>
                    <p class="card-text">Pelajari lebih lanjut tentang sistem pakar ini.</p>
                    <a href="#" class="btn btn-primary">Baca Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>