<?php
session_start();
require_once(__DIR__ . '/includes/functions.php');
require_once(__DIR__ . '/includes/header.php');

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['gejala'])) {
    header('Location: konsultasi.php');
    exit();
}

$gejala_terpilih = $_POST['gejala'];
$hasil_diagnosa = hitungNaiveBayes($gejala_terpilih);

// Simpan riwayat diagnosa
$user_id = $_SESSION['user_id'];
$penyakit_id = $hasil_diagnosa['id'];
$tanggal = date('Y-m-d');
$gejala_str = implode(',', array_map(fn($gejala) => 'G' . $gejala, $gejala_terpilih));

$insert_query = "INSERT INTO riwayat (user_id, penyakit_id, tanggal, gejala) 
                 VALUES ('$user_id', '$penyakit_id', '$tanggal', '$gejala_str')";
mysqli_query($conn, $insert_query);
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
    
    .result-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .result-header {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
    }
    
    .diagnosis-alert {
        background-color: rgba(52, 152, 219, 0.1);
        border-left: 4px solid var(--primary-color);
        border-radius: 8px;
    }
    
    .disease-name {
        color: var(--primary-color);
        font-weight: 700;
        margin: 1rem 0;
        font-size: 1.5rem; /* Sama dengan ukuran sebelumnya */
    }
    
    .probability-badge {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem; /* Sama dengan ukuran sebelumnya */
    }
    
    .section-title {
        color: var(--primary-color);
        font-weight: 600;
        margin: 1.5rem 0 1rem;
        position: relative;
        padding-bottom: 0.5rem;
        font-size: 1.25rem; /* Sama dengan ukuran sebelumnya */
    }
    
    .section-title:after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: var(--primary-color);
        position: absolute;
        bottom: 0;
        left: 0;
    }
    
    .table {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 1rem; /* Sama dengan ukuran sebelumnya */
    }
    
    .table thead th {
        background-color: var(--primary-color);
        color: white;
        border: none;
        font-size: 1rem; /* Sama dengan ukuran sebelumnya */
    }
    
    .table tbody td {
        font-size: 1rem; /* Sama dengan ukuran sebelumnya */
    }
    
    .btn-result {
        border-radius: 50px;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 1rem; /* Sama dengan ukuran sebelumnya */
    }
    
    .probability-percent {
        font-weight: 600;
        color: var(--primary-color);
    }
</style>

<div class="container mt-4">
    <div class="result-card">
        <div class="result-header">
            <div class="d-flex align-items-center">
                <i class="fas fa-clipboard-check fa-2x me-3" style="margin-right: 1rem !important;"></i>
                <h4 class="mb-0" style="font-size: 1.5rem;">Hasil Diagnosis Penyakit Gigi</h4>
            </div>
        </div>
        
        <div class="card-body">
            <div class="diagnosis-alert alert mb-4">
                <div class="d-flex align-items-center">                    <div>
                        <h5 class="mb-2" style="font-size: 1.25rem;">Berdasarkan gejala yang Anda pilih, kemungkinan Anda menderita:</h5>
                        <h3 class="disease-name"><?= $hasil_diagnosa['nama_penyakit'] ?></h3>
                        <span class="probability-badge">
                            Tingkat Kemiripan: <?= number_format($hasil_diagnosa['probabilitas'] * 100, 2) ?>%
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h5 class="section-title"><i class="fas fa-file-medical me-2" style="margin-right: 0.5rem !important;"></i>Deskripsi Penyakit</h5>
                <div class="card card-body bg-light" style="font-size: 1rem;">
                    <?= nl2br(htmlspecialchars($hasil_diagnosa['deskripsi'])) ?>
                </div>
            </div>
            
            <div class="mt-4">
                <h5 class="section-title"><i class="fas fa-lightbulb me-2"  style="margin-right: 0.5rem !important;"></i>Solusi Penanganan</h5>
                <div class="card card-body bg-light" style="font-size: 1rem;">
                    <?= nl2br(htmlspecialchars($hasil_diagnosa['solusi'])) ?>
                </div>
            </div>
            
            <div class="mt-4">
                <h5 class="section-title"><i class="fas fa-list-ol me-2"  style="margin-right: 0.5rem !important;"></i>Probabilitas Penyakit Lain</h5>
                <p style="font-size: 1rem;">Berikut kemungkinan penyakit lain berdasarkan gejala yang Anda pilih:</p>
                
                <?php if (!empty($hasil_diagnosa['probabilitas_lain'])): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="60%">Nama Penyakit</th>
                                    <th width="30%">Probabilitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasil_diagnosa['probabilitas_lain'] as $index => $penyakit): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($penyakit['nama_penyakit']) ?></td>
                                        <td class="probability-percent">
                                            <?= number_format($penyakit['probabilitas'] * 100, 2) ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary" style="font-size: 1rem;">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada penyakit lain yang terdeteksi dengan probabilitas signifikan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-footer text-center">
            <a href="konsultasi.php" class="btn btn-primary btn-result me-3">
                <i class="fas fa-redo me-2"></i> Konsultasi Lagi
            </a>
            <a href="dashboard.php" class="btn btn-secondary btn-result">
                <i class="fas fa-home me-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>