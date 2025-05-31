<?php
session_start();
require_once(__DIR__ . '/includes/functions.php');
require_once(__DIR__ . '/includes/header.php');

// Ambil semua penyakit
$query = "SELECT * FROM penyakit ORDER BY kode_penyakit";
$result = mysqli_query($conn, $query);
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
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
        background-color: #f5f5f5;
        color: #333;
        line-height: 1.6;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    header {
        background-color: var(--primary-color);
        color: white;
        padding: 20px 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .logo h1 {
        font-size: 24px;
        font-weight: 700;
    }
    
    .logo p {
        font-size: 14px;
        opacity: 0.9;
    }
    
    .auth-buttons a {
        color: white;
        text-decoration: none;
        margin-left: 15px;
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .auth-buttons a:first-child {
        background-color: var(--secondary-color);
    }
    
    .auth-buttons a:last-child {
        border: 1px solid white;
    }
    
    .auth-buttons a:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(0, 0, 0, 0.15);
    }
    
    .hero {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        border-radius: 15px;
        padding: 3rem 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin: 2rem auto 3rem;
        position: relative;
        overflow: hidden;
        max-width: 1200px;
    }
    
    .hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .hero::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    
    .hero h2 {
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
        font-size: 32px;
    }
    
    .hero p {
        font-size: 1.4rem;
        margin-bottom: 2rem;
        position: relative;
        z-index: 2;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .divider {
        height: 3px;
        background: rgba(255, 255, 255, 0.2);
        width: 100px;
        margin: 1.5rem auto;
        border-radius: 3px;
    }
    
    .disease-info {
        padding: 50px 0;
    }
    
    .section-title {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
        font-size: 28px;
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
    
    .disease-card {
        background: white;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 30px;
        position: relative;
    }
    
    .disease-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .disease-card h3 {
        color: var(--dark-color);
        margin-bottom: 15px;
        font-size: 22px;
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 10px;
        font-weight: 700;
    }
    
    .disease-section {
        margin-bottom: 20px;
    }
    
    .disease-section h4 {
        color: var(--secondary-color);
        margin-bottom: 10px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .disease-section ul {
        padding-left: 20px;
    }
    
    .disease-section ul li {
        margin-bottom: 8px;
        position: relative;
    }
    
    .disease-section ul li::before {
        content: "•";
        color: var(--secondary-color);
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }
    
    .prevention-tips {
        background-color: #e8f4f3;
        padding: 2rem;
        border-radius: 15px;
        margin: 40px 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .prevention-tips h3 {
        color: var(--primary-color);
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: 700;
    }
    
    .prevention-tips ol {
        padding-left: 20px;
        list-style-type: none; 
        counter-reset: item; 
    }

    .prevention-tips ol li {
        margin-bottom: 10px;
        padding-left: 10px;
        position: relative;
    }

    .prevention-tips ol li::before {
        content: counter(item) "."; 
        counter-increment: item; 
        color: var(--primary-color);
        font-weight: bold;
        position: absolute;
        left: -20px;
    }
    
    .cta-section {
        text-align: center;
        margin: 40px 0;
    }
    
    .cta-section p {
        font-size: 1.4rem;
        margin-bottom: 20px;
        color: var(--dark-color);
    }
    
    .cta-buttons a {
        display: inline-block;
        padding: 0.8rem 2rem;
        margin: 0 0.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .cta-buttons a:first-child {
        background-color: var(--secondary-color);
        color: white;
    }
    
    .cta-buttons a:last-child {
        border: 1px solid var(--secondary-color);
        color: var(--secondary-color);
    }
    
    .cta-buttons a:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Footer */
    .footer-nav {
        text-align: center;
        margin-top: 50px;
        padding-top: 20px;
        border-top: 1px solid #ddd;
    }
    
    .footer-nav a {
        color: var(--primary-color);
        text-decoration: none;
        margin: 0 15px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .footer-nav a:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }
    
    /* Disclaimer */
    .disclaimer {
        text-align: center;
        font-size: 14px;
        color: #777;
        margin-top: 20px;
        padding-bottom: 30px;
        font-style: italic;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
        }
        
        .auth-buttons {
            margin-top: 15px;
        }
        
        .auth-buttons a {
            display: inline-block;
            margin: 5px;
        }
        
        .hero h2 {
            font-size: 28px;
        }
        
        .hero p {
            font-size: 1.2rem;
        }
        
        .cta-buttons a {
            display: block;
            margin: 10px auto;
            max-width: 250px;
        }
        
        .disease-card {
            padding: 1.5rem;
        }
    }
</style>
    <section class="hero">
        <div class="container" style="text-align: center;">
            <h2>Informasi Penyakit Gigi</h2>
            <p>Pelajari berbagai jenis penyakit gigi dan solusi penanganannya</p>
        </div>
    </section>
    
    
    <section class="disease-info">
        <div class="container">
            <h2 class="section-title">Daftar Penyakit Gigi Umum</h2>
            
            <?php 
            // Query untuk mengambil data dari tabel penyakit
            $diseases_query = mysqli_query($conn, "SELECT * FROM penyakit");
            $counter = 1;

            while ($disease = mysqli_fetch_assoc($diseases_query)): 
            ?>
                <div class="disease-card">
                    <h3><?= $counter++ ?>. <?= htmlspecialchars($disease['nama_penyakit']) ?></h3>
                    
                    <div class="disease-section">
                        <h4>Deskripsi:</h4>
                        <p><?= nl2br(htmlspecialchars($disease['deskripsi'])) ?></p>
                    </div>
                    
                    <div class="disease-section">
                        <h4>Penanganan:</h4>
                        <p><?= nl2br(htmlspecialchars($disease['solusi'])) ?></p>
                    </div>
                </div>
            <?php 
            endwhile;

            mysqli_free_result($diseases_query);
            ?>
            
            <!-- Pencegahan-->
            <div class="prevention-tips">
                <h3>Tips Pencegahan</h3>
                <ol>
                    <li>Sikat gigi 2 kali sehari dengan teknik yang benar</li>
                    <li>Gunakan benang gigi setiap hari</li>
                    <li>Rutin kontrol ke dokter gigi 6 bulan sekali</li>
                    <li>Batasi konsumsi makanan dan minuman manis</li>
                    <li>Berhenti merokok</li>
                </ol>
            </div>
            

            <div class="cta-section">
                <p>Butuh diagnosa lebih personal?</p>
                <div class="cta-buttons">
                    <a href="konsultasi.php">Coba Diagnosa Mandiri</a>
                    <a href="https://www.google.com/search?q=dokter+gigi+terdekat" target="_blank">Konsultasi dengan Dokter Gigi</a>
                </div>
            </div>
            
            <!-- Footer-->
            <div class="footer-nav">
                <a href="index.php">Kembali ke Website</a>
                <a href="tentang_sistem.php">Pelajari Tentang Sistem</a>
            </div>
            
            <!-- Disclaimer -->
            <p class="disclaimer">Catatan: Informasi ini bersifat edukatif dan tidak menggantikan konsultasi profesional. Jika mengalami gejala serius, segera hubungi dokter gigi.</p>
        </div>
    </section>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const diseaseCards = document.querySelectorAll('.disease-card');
            
            if (window.innerWidth < 768) {
                diseaseCards.forEach(card => {
                    const title = card.querySelector('h3');
                    title.style.cursor = 'pointer';
                    
                    title.addEventListener('click', function() {
                        card.classList.toggle('expanded');
                    });
                });
            }
        });
    </script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>