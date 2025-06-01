<?php
ob_start(); // Mulai output buffering
$page_title = "Riwayat Diagnosis Pengguna";
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

// Handle flash messages
if (isset($_SESSION['flash_message'])) {
    $flash_type = $_SESSION['flash_message']['type'];
    $flash_msg = $_SESSION['flash_message']['message'];
    unset($_SESSION['flash_message']);
}

// ambil id user
$user_id = (int) $_GET['id'];
$query = "select * from users where id=$user_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Data pengguna tidak ditemukan'
    ];
    header('Location: riwayat.php');
    exit();
}

// Konfigurasi pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Pastikan tidak kurang dari 1
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Pencarian riwayat
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_param = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = " AND (p.nama_penyakit LIKE '%$search%' OR r.gejala LIKE '%$search%')";
    $search_param = '&search=' . urlencode($search);
}

// Hitung total data dengan kondisi pencarian
$total_query = "SELECT COUNT(*) as total FROM riwayat r
                LEFT JOIN penyakit p ON r.penyakit_id = p.id
                WHERE r.user_id = $user_id $search_condition";
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total = $total_data['total'];
$pages = ceil($total / $per_page);

// Query data dengan pagination dan pencarian
$query = "SELECT r.*, p.nama_penyakit, p.kode_penyakit 
          FROM riwayat r
          LEFT JOIN penyakit p ON r.penyakit_id = p.id
          WHERE r.user_id = $user_id $search_condition
          ORDER BY r.tanggal DESC
          LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid py-4">
    <!-- Flash Message -->
    <?php if (isset($flash_type) && isset($flash_msg)): ?>
    <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show" role="alert">
        <?= $flash_msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i> Riwayat Diagnosis: <?= $data['nama']?>
                        </h5>
                        <form class="d-flex" method="get" action="">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Cari riwayat..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-light ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="riwayat_detail.php?id=<?= $user_id?>" class="btn btn-sm btn-outline-light ms-2">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Hasil Diagnosis</th>
                                    <th>Gejala</th>
                                    <th width="150">Tanggal</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = $start + 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?php if(!empty($row['nama_penyakit'])): ?>
                                            <span class="badge bg-primary"><?= htmlspecialchars($row['kode_penyakit']) ?></span>
                                            <?= htmlspecialchars($row['nama_penyakit']) ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak Terdiagnosis</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" 
                                             data-bs-toggle="tooltip" data-bs-placement="top" 
                                             title="<?= htmlspecialchars($row['gejala']) ?>">
                                            <?= htmlspecialchars(substr($row['gejala'], 0, 50)) ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="riwayat_detail.php?id=<?= $user_id ?>&detail=<?= $row['id'] ?>" 
                                               class="btn btn-outline-primary" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-danger" 
                                               onclick="confirmDelete(<?= $row['id'] ?>)" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-4">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?id=<?= $user_id?>&page=<?= $page-1 ?><?= $search_param ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?id=<?= $user_id?>&page=<?= $i ?><?= $search_param ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?id=<?= $user_id ?>&page=<?= $page+1 ?><?= $search_param ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <div class="display-4 text-muted mb-4">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5>Belum ada riwayat diagnosis untuk <?= $data['nama']?></h5>
                        <p class="text-muted">Belum ada pengguna yang melakukan diagnosis</p>
                        <?php if (!empty($search)): ?>
                            <a href="riwayat_detail.php?id=<?= $user_id ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke semua riwayat
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Aktifkan tooltip
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus riwayat ini?')) {
        window.location.href = 'riwayat_hapus.php?id=' + id;
    }
}
</script>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
ob_end_flush(); // Akhir output buffering
?>
