<?php
session_start();
require_once(__DIR__ . '/includes/functions.php');

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}
require_once(__DIR__ . '/includes/header.php');


// Ambil semua gejala dari database
$gejala_query = "SELECT * FROM gejala";
$gejala_result = mysqli_query($conn, $gejala_query);
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
    
    .consultation-hero {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        padding: 2rem 0;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .consultation-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .consultation-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
    }
    
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        margin-top: 0.2em;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .form-check-label {
        margin-left: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .form-check-label:hover {
        color: var(--primary-color);
    }
    
    .gejala-item {
        padding: 0.8rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .gejala-item:hover {
        background-color: rgba(52, 152, 219, 0.1);
        transform: translateX(5px);
    }
    
    .btn-diagnosa {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        border-radius: 50px;
        padding: 10px 30px;
        color: white;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-diagnosa:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .card-footer {
        background-color: rgba(0, 0, 0, 0.03);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        text-align: center;
    }
</style>

<div class="container mt-4">
    <div class="consultation-hero text-center">
        <h2 style="font-weight: 700;">Konsultasi Penyakit Gigi</h2>
        <p class="lead">Pilih gejala yang Anda alami untuk mendapatkan diagnosis</p>
    </div>
    
    <form action="hasil_diagnosa.php" method="post">
        <div class="consultation-card">
            <div class="consultation-header">
                <h4><i class="fas fa-clipboard-check me-2"></i> Pilih Gejala yang Anda Alami</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php while ($gejala = mysqli_fetch_assoc($gejala_result)): ?>
                    <div class="col-md-6">
                        <div class="gejala-item">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="gejala[]" 
                                       id="gejala<?= $gejala['id'] ?>" value="<?= $gejala['id'] ?>">
                                <label class="form-check-label" for="gejala<?= $gejala['id'] ?>">
                                    <?= "<strong>[" . $gejala['kode_gejala'] . "]</strong> " . $gejala['nama_gejala'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-diagnosa">
                    <i class="fas fa-stethoscope me-2"></i> Diagnosis Sekarang
                </button>
            </div>
        </div>
    </form>
</div>

<script src="assets/js/validasi.js"></script>

<?php require_once 'includes/footer.php'; ?>