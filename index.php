<?php
session_start();
require_once(__DIR__ . '/includes/functions.php');
require_once(__DIR__ . '/includes/header.php');
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
    
    .hero-section {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        border-radius: 15px;
        padding: 3rem 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    
    .display-4 {
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
    }
    
    .lead {
        font-size: 1.4rem;
        margin-bottom: 2rem;
        position: relative;
        z-index: 2;
    }
    
    .divider {
        height: 3px;
        background: rgba(255, 255, 255, 0.2);
        width: 100px;
        margin: 1.5rem auto;
        border-radius: 3px;
    }
    
    .btn-hero {
        border-radius: 50px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        margin: 0 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 2;
        border: none;
    }
    
    .btn-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(0, 0, 0, 0.15);
    }
    
    .btn-hero:active {
        transform: translateY(1px);
    }
    
    .feature-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        height: 100%;
        position: relative;
        background: white;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .feature-card .card-body {
        padding: 2rem;
    }
    
    .feature-card .card-title {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 1rem;
        font-size: 1.3rem;
    }
    
    .feature-card .card-text {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }
    
    .feature-card .btn {
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
    }
    
    .card-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .section-title {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 3px;
    }
</style>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section text-center">
        <h1 class="display-4">Sistem Pakar Diagnosa Penyakit Gigi</h1>
        <p class="lead">Diagnosa mandiri penyakit gigi dengan metode Naïve Bayes yang akurat</p>
        <div class="divider"></div>
        <p class="mb-4">Mulai perjalanan kesehatan gigi Anda sekarang</p>
        <div class="mt-4">
            <a href="register.php" class="btn btn-hero btn-light">Daftar Sekarang</a>
            <a href="login.php" class="btn btn-hero btn-primary">Masuk</a>
        </div>
    </div>

    <!-- Features Section -->
    <h2 class="section-title text-center mb-5">Layanan Kami</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="feature-card">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h5 class="card-title">Diagnosa Mandiri</h5>
                    <p class="card-text">Identifikasi potensi masalah gigi Anda melalui serangkaian pertanyaan gejala yang sederhana.</p>
                    <a href="login.php" class="btn btn-outline-primary">Mulai Diagnosa</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <i class="fas fa-book-medical"></i>
                    </div>
                    <h5 class="card-title">Informasi Penyakit</h5>
                    <p class="card-text">Pelajari berbagai jenis penyakit gigi, gejala, dan solusi penanganannya.</p>
                    <a href="informasi_penyakit.php" class="btn btn-outline-primary">Lihat Daftar</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h5 class="card-title">Tentang Sistem</h5>
                    <p class="card-text">Temukan bagaimana sistem pakar ini bekerja dan manfaatnya untuk Anda.</p>
                    <a href="tentang_sistem.php" class="btn btn-outline-primary">Baca Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>