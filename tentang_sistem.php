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
    
    .feature-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .section-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .section-title:after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: var(--primary-color);
        margin-top: 10px;
    }
    
    .how-it-works-step {
        display: flex;
        margin-bottom: 2rem;
    }
    
    .step-number {
        background: var(--primary-color);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .benefit-card {
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s;
    }
    
    .benefit-card:hover {
        transform: translateX(5px);
    }
</style>

<div class="hero-section">
    <div class="container text-center">
        <h1 style="font-weight: 700; margin-bottom: 1rem;">Tentang Sistem Pakar Kami</h1>
        <p class="lead" style="font-size: 1.2rem; margin-bottom: 2rem;">Temukan bagaimana sistem pakar ini bekerja dan manfaatnya</p>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="section-title">Apa Itu Sistem Pakar Diagnosa Penyakit Gigi?</h2>
                    <p>Sistem pakar ini adalah aplikasi berbasis web yang menggunakan metode Naïve Bayes untuk membantu mendiagnosa penyakit gigi berdasarkan gejala yang dialami pengguna. Sistem ini dirancang untuk memberikan kemudahan dalam mengidentifikasi potensi masalah gigi secara mandiri sebelum berkonsultasi dengan dokter.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Bagaimana Sistem Ini Bekerja?</h2>
                    
                    <div class="how-it-works-step">
                        <div class="step-number">1</div>
                        <div>
                            <h5>Input Gejala</h5>
                            <p>Pengguna mengisi form gejala yang dialami melalui serangkaian pertanyaan sederhana.</p>
                        </div>
                    </div>
                    
                    <div class="how-it-works-step">
                        <div class="step-number">2</div>
                        <div>
                            <h5>Proses Perhitungan</h5>
                            <p>Sistem menganalisis gejala menggunakan algoritma Naïve Bayes untuk menghitung probabilitas penyakit.</p>
                        </div>
                    </div>
                    
                    <div class="how-it-works-step">
                        <div class="step-number">3</div>
                        <div>
                            <h5>Hasil Diagnosa</h5>
                            <p>Sistem menampilkan hasil diagnosa beserta persentase kemungkinan dan saran penanganan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Metode Naïve Bayes</h2>
                    <p>Naïve Bayes adalah algoritma klasifikasi probabilistik yang sederhana namun kuat, berdasarkan pada Teorema Bayes dengan asumsi "naif" bahwa semua fitur (gejala) saling independen.</p>
                    
                    <div class="mt-4">
                        <h5>Rumus Naïve Bayes:</h5>
                        <p>P(A|B) = [P(B|A) × P(A)] / P(B)</p>
                        <p>Dimana:</p>
                        <ul>
                            <li>P(A|B): Probabilitas penyakit A diberikan gejala B</li>
                            <li>P(B|A): Probabilitas gejala B pada penyakit A</li>
                            <li>P(A): Probabilitas awal penyakit A</li>
                            <li>P(B): Probabilitas gejala B</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Manfaat Sistem Ini</h2>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card benefit-card mb-3">
                                <div class="card-body">
                                    <div class="feature-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <h5>Diagnosa Cepat</h5>
                                    <p>Mendapatkan analisis kondisi gigi dalam hitungan menit tanpa perlu antri.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card benefit-card mb-3">
                                <div class="card-body">
                                    <div class="feature-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <h5>Akses Mudah</h5>
                                    <p>Bisa digunakan kapan saja dan di mana saja hanya dengan perangkat berbasis web.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card benefit-card mb-3">
                                <div class="card-body">
                                    <div class="feature-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h5>Akurasi Tinggi</h5>
                                    <p>Dengan metode Naïve Bayes, sistem memberikan hasil dengan tingkat akurasi yang baik.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12 text-center">
            <a href="konsultasi.php" class="btn btn-primary btn-lg">Mulai Diagnosa Sekarang</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>