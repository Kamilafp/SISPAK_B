<?php
ob_start(); // Mulai output buffering
$page_title = "Detail Riwayat Diagnosis";
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/layout/header_layout.php');

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek login dan role admin
if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'pakar') {
    header('Location: /../../login.php');
    exit();
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'ID riwayat tidak valid!'
    ];
    header('Location: riwayat.php');
    exit();
}

// Query untuk mengambil detail riwayat diagnosis
$query = "SELECT r.*, u.nama as nama_user, u.email, 
                 p.nama_penyakit, p.kode_penyakit, p.deskripsi, p.solusi
          FROM riwayat r
          JOIN users u ON r.user_id = u.id
          LEFT JOIN penyakit p ON r.penyakit_id = p.id
          WHERE r.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Data riwayat tidak ditemukan!'
    ];
    header('Location: riwayat.php');
    exit();
}

$data = mysqli_fetch_assoc($result);

// Pisahkan gejala yang dipilih
$gejala_array = [];
if (!empty($data['gejala'])) {
    $temp = explode(',', $data['gejala']);
    foreach ($temp as $g) {
        $g = trim($g);
        if ($g !== '') {
            $gejala_array[] = $g;
        }
    }
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i> Detail Riwayat Diagnosis
                        </h5>
                        <a href="riwayat.php" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Informasi Pengguna -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user me-2"></i> Informasi Pengguna
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="120"><strong>ID Pengguna</strong></td>
                                            <td>:</td>
                                            <td><?= htmlspecialchars($data['user_id']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama</strong></td>
                                            <td>:</td>
                                            <td><?= htmlspecialchars($data['nama_user']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email</strong></td>
                                            <td>:</td>
                                            <td><?= htmlspecialchars($data['email']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal</strong></td>
                                            <td>:</td>
                                            <td>
                                                <?= date('d F Y', strtotime($data['tanggal'])) ?> 
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Hasil Diagnosis -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-diagnosis me-2"></i> Hasil Diagnosis
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($data['penyakit_id']) && !empty($data['nama_penyakit'])): ?>
                                        <div class="text-center">
                                            <span class="badge bg-primary fs-6 mb-2">
                                                <?= htmlspecialchars($data['kode_penyakit']) ?>
                                            </span>
                                        </div>
                                        <h5 class="text-center text-primary mb-3">
                                            <?= htmlspecialchars($data['nama_penyakit']) ?>
                                        </h5>
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td width="120"><strong>ID Penyakit</strong></td>
                                                <td>:</td>
                                                <td><?= htmlspecialchars($data['penyakit_id']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kode Penyakit</strong></td>
                                                <td>:</td>
                                                <td><?= htmlspecialchars($data['kode_penyakit']) ?></td>
                                            </tr>
                                        </table>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <i class="fas fa-times-circle fa-3x text-secondary"></i>
                                            </div>
                                            <h5 class="text-secondary">Tidak Terdiagnosis</h5>
                                            <p class="text-muted">Gejala yang dipilih tidak mengarah pada penyakit tertentu</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gejala yang Dipilih -->
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list-ul me-2"></i> Gejala yang Dipilih
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($gejala_array)): ?>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <?php foreach ($gejala_array as $index => $gejala): ?>
                                                <span class="badge bg-secondary">
                                                     <?= htmlspecialchars(trim($gejala)) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <small class="text-muted">
                                                <strong>Total gejala:</strong> <?= count($gejala_array) ?> gejala
                                            </small>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Tidak ada gejala yang tercatat</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($data['penyakit_id']) && !empty($data['nama_penyakit'])): ?>
                    <!-- Deskripsi Penyakit -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-medical me-2"></i> Deskripsi Penyakit
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($data['deskripsi'])): ?>
                                        <p class="text-justify"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">Deskripsi penyakit tidak tersedia</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Solusi/Pengobatan -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-medkit me-2"></i> Solusi & Pengobatan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($data['solusi'])): ?>
                                        <p class="text-justify"><?= nl2br(htmlspecialchars($data['solusi'])) ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">Solusi pengobatan tidak tersedia</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Aksi -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <div>
                                    <button onclick="window.print()" class="btn btn-info me-2">
                                        <i class="fas fa-print me-1"></i> Cetak
                                    </button>
                                    <button onclick="confirmDelete(<?= $data['id'] ?>)" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i> Hapus Riwayat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus riwayat ini?\n\nData yang dihapus tidak dapat dikembalikan!')) {
        window.location.href = 'riwayat_hapus.php?id=' + id;
    }
}

// Print styling
window.addEventListener('beforeprint', function() {
    // Hide buttons when printing
    document.querySelectorAll('.btn').forEach(btn => {
        btn.style.display = 'none';
    });
    document.querySelector('.breadcrumb').style.display = 'none';
});

window.addEventListener('afterprint', function() {
    // Show buttons after printing
    document.querySelectorAll('.btn').forEach(btn => {
        btn.style.display = '';
    });
    document.querySelector('.breadcrumb').style.display = '';
});
</script>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
ob_end_flush(); // Akhir output buffering
?>