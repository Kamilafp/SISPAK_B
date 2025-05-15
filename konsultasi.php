<?php
session_start();
require_once(__DIR__ . '/includes/functions.php');
require_once(__DIR__ . '/includes/header.php');

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Ambil semua gejala dari database
$gejala_query = "SELECT * FROM gejala";
$gejala_result = mysqli_query($conn, $gejala_query);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Konsultasi Penyakit Gigi</h2>
    
    <form action="hasil_diagnosa.php" method="post">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Pilih Gejala yang Anda Alami</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php while ($gejala = mysqli_fetch_assoc($gejala_result)): ?>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="gejala[]" 
                                   id="gejala<?= $gejala['id'] ?>" value="<?= $gejala['id'] ?>">
                            <label class="form-check-label" for="gejala<?= $gejala['id'] ?>">
                                <?= "[" . $gejala['kode_gejala'] . "] " . $gejala['nama_gejala'] ?>
                            </label>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Diagnosa Sekarang</button>
            </div>
        </div>
    </form>
</div>
<script src="assets/js/validasi.js"></script>

<?php require_once 'includes/footer.php'; ?>