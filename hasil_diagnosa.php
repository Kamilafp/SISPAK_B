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

// Gabungkan ID gejala yang dipilih menjadi string, misalnya: "G01,G03,G07"
$gejala_str = implode(',', array_map(fn($gejala) => 'G' . $gejala, $gejala_terpilih));

$gejala_terpilih = $_POST['gejala'];
$hasil_diagnosa = hitungNaiveBayes($gejala_terpilih);

// Simpan riwayat diagnosa
$user_id = $_SESSION['user_id'];
$penyakit_id = $hasil_diagnosa['id'];
$tanggal = date('Y-m-d');

$insert_query = "INSERT INTO riwayat (user_id, penyakit_id, tanggal, gejala) 
                 VALUES ('$user_id', '$penyakit_id', '$tanggal', '$gejala_str')";
mysqli_query($conn, $insert_query);

?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4>Hasil Diagnosa</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5>Berdasarkan gejala yang Anda pilih, kemungkinan Anda menderita:</h5>
                <h3 class="text-primary"><?= $hasil_diagnosa['nama_penyakit'] ?></h3>
                <p>Dengan probabilitas: <?= number_format($hasil_diagnosa['probabilitas'] * 100, 2) ?>%</p>
            </div>
            
            <div class="mt-4">
                <h5>Deskripsi Penyakit:</h5>
                <p><?= $hasil_diagnosa['deskripsi'] ?></p>
            </div>
            
            <div class="mt-4">
                <h5>Solusi Penanganan:</h5>
                <p><?= $hasil_diagnosa['solusi'] ?></p>
            </div>
            <div class="mt-4">
            <h5>Probabilitas Kemungkinan Penyakit Lain</h5>
                <p>Berikut adalah daftar penyakit lain yang mungkin juga terkait dengan gejala yang Anda pilih:</p>
                <?php if (!empty($hasil_diagnosa['probabilitas_lain'])): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penyakit</th>
                                    <th>Probabilitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasil_diagnosa['probabilitas_lain'] as $index => $penyakit): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($penyakit['nama_penyakit']) ?></td>
                                        <td><?= number_format($penyakit['probabilitas'] * 100, 2) ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Tidak ada penyakit lain yang terdeteksi dengan probabilitas signifikan.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer">
            <a href="konsultasi.php" class="btn btn-primary">Konsultasi Lagi</a>
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
