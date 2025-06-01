<?php
ob_start(); // Mulai output buffering
$page_title = "Detail Diagnosis Pengguna";
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/layout/header_layout.php');

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek login dan role admin/pakar
if (!isLoggedIn() || $_SESSION['role'] !=='pakar') {
    header('Location: /../../login.php');
    exit();
}

// ambil parameter user_id dan detail_id dri URL
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$detail_id = isset($_GET['detail']) ? (int) $_GET['detail'] : 0;

if (!$user_id || !$detail_id) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Data pengguna tidak ditemukan'
    ];
    header('Location: riwayat.php');
    exit();
}

// ambil data detail riwayat berdasarkan user_id dan detail_id
$query = "SELECT r.*, p.nama_penyakit, p.deskripsi, p.solusi
FROM riwayat r
LEFT JOIN penyakit p ON r.penyakit_id = p.id
WHERE r.id = ? AND r.user_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $detail_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$detail = mysqli_fetch_assoc($result);

if (!$detail) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Data riwayat tidak ditemukan'
    ];
    header('Location: riwayat_user.php');
    exit();
}
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

    .history-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .history-header {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
    }

    .btn-back {
        background-color: white;
        color: var(--primary-color);
        border-radius: 50px;
        padding: 5px 15px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background-color: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    .table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table thead th {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }

    .table tbody tr {
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .table tbody td {
        vertical-align: middle;
        border-top: none;
        padding: 1rem;
    }

    .btn-detail {
        background-color: var(--primary-color);
        color: white;
        border-radius: 50px;
        padding: 5px 15px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-detail:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        color: white;
    }

    .info-card {
        border-radius: 10px;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s;
    }

    .info-card:hover {
        transform: translateX(5px);
    }

    .alert-info {
        background-color: rgba(52, 152, 219, 0.1);
        border-color: rgba(52, 152, 219, 0.2);
        color: var(--secondary-color);
    }
    .probability-badge {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
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
    <div class="history-card">
        <div class="history-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-file-alt me-2" style="margin-right: 0.5rem !important;"></i> Detail
                    Konsultasi</h4>
                <a href="riwayat_user.php?id=<?= $user_id ?>" class="btn btn-back">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="info-card card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-dark"><i class="fas fa-info-circle me-2"
                                    style="margin-right: 0.5rem !important;"></i>Informasi Konsultasi</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="30%">Tanggal</th>
                                    <td><?= date('d F Y', strtotime($detail['tanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Penyakit</th>
                                    <td><?= htmlspecialchars($detail['nama_penyakit'] ?: 'Tidak Terdiagnosis') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="info-card card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-dark"><i class="fas fa-clipboard-list me-2"
                                    style="margin-right: 0.5rem !important;"></i>Gejala yang Dipilih</h5>
                            <div class="alert alert-secondary p-3">
                                <?php
                                $kode_gejala = explode(',', $detail['gejala']);
                                // normalisasi kode jadi 3 digit: G01, dst
                                $kode_gejala = array_map(function($kode) {
                                    $kode = strtoupper(trim($kode));
                                    // Jika G1 jadi G01
                                    if (preg_match('/^G\d$/', $kode)) {
                                        return 'G0' . substr($kode, 1);
                                    }
                                    return $kode;
                                }, $kode_gejala);

                                $gejala_list = [];

                                if (!empty($kode_gejala)) {
                                    $placeholders = implode(',', array_fill(0, count($kode_gejala), '?'));
                                    $query_gejala = "SELECT kode_gejala, nama_gejala FROM gejala WHERE kode_gejala IN ($placeholders)";
                                    $stmt_gejala = mysqli_prepare($conn, $query_gejala);
                                    mysqli_stmt_bind_param($stmt_gejala, str_repeat('s', count($kode_gejala)), ...$kode_gejala);
                                    mysqli_stmt_execute($stmt_gejala);
                                    $result_gejala = mysqli_stmt_get_result($stmt_gejala);

                                    while ($row = mysqli_fetch_assoc($result_gejala)) {
                                        $gejala_list[$row['kode_gejala']] = $row['nama_gejala'];
                                    }

                                    if (!empty($gejala_list)) {
                                        echo '<ul class="mb-0">';
                                        foreach ($kode_gejala as $kode) {
                                            $nama = $gejala_list[$kode] ?? 'Tidak diketahui';
                                            echo '<li><strong>' . htmlspecialchars($kode) . ' -</strong> ' . htmlspecialchars($nama) . '</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo 'Tidak ada data gejala tersimpan';
                                    }
                                } else {
                                    echo 'Tidak ada data gejala tersimpan';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="info-card card">
                        <div class="card-body">
                            <h5 class="card-title text-dark"><i class="fas fa-book me-2"
                                    style="margin-right: 0.5rem !important;"></i>Deskripsi Penyakit</h5>
                            <div class="card-text p-3 bg-light rounded">
                                <?= nl2br(htmlspecialchars($detail['deskripsi'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="info-card card">
                        <div class="card-body">
                            <h5 class="card-title text-dark"><i class="fas fa-lightbulb me-2"
                                    style="margin-right: 0.5rem !important;"></i>Solusi Penanganan</h5>
                            <div class="card-text p-3 bg-light rounded">
                                <?= nl2br(htmlspecialchars($detail['solusi'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
ob_end_flush(); // Akhir output buffering
?>
