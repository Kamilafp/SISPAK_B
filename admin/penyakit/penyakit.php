<?php
ob_start(); // Mulai output buffering
$page_title = "Manajemen Penyakit";
require_once(__DIR__ . '/../../includes/functions.php');
require_once(__DIR__ . '/../layout/header_layout.php');

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek login dan role admin
if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'pakar') {
    header('Location: /../../login.php');
    exit();
}

// Handle flash messages
if (isset($_SESSION['flash_message'])) {
    $flash_type = $_SESSION['flash_message']['type'];
    $flash_msg = $_SESSION['flash_message']['message'];
    unset($_SESSION['flash_message']);
}

// Tambah penyakit baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $solusi = trim($_POST['solusi']);
    
    // Validasi format kode penyakit (contoh: P01)
    if (!preg_match('/^P\d{2}$/', $kode)) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Format kode penyakit harus P diikuti 2 angka (contoh: P01)'
        ];
        header('Location: penyakit.php');
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO penyakit (kode_penyakit, nama_penyakit, deskripsi, solusi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $kode, $nama, $deskripsi, $solusi);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Penyakit berhasil ditambahkan'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Gagal menambahkan penyakit: ' . $stmt->error
        ];
    }
    $stmt->close();
    header('Location: penyakit.php');
    exit();
}

// Hapus penyakit
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Cek apakah penyakit digunakan di aturan
    $check_query = "SELECT COUNT(*) as total FROM aturan WHERE penyakit_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $check_result = $stmt->get_result();
    $check_data = $check_result->fetch_assoc();
    $stmt->close();
    
    if ($check_data['total'] > 0) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Tidak dapat menghapus karena penyakit digunakan dalam aturan'
        ];
    } else {
        $stmt = $conn->prepare("DELETE FROM penyakit WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Penyakit berhasil dihapus'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal menghapus penyakit: ' . $stmt->error
            ];
        }
        $stmt->close();
    }
    header('Location: penyakit.php');
    exit();
}

// Konfigurasi pagination
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Pastikan tidak kurang dari 1
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Pencarian penyakit
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_param = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = " WHERE nama_penyakit LIKE '%$search%' OR kode_penyakit LIKE '%$search%'";
    $search_param = '&search=' . urlencode($search);
}

// Hitung total data dengan kondisi pencarian
$total_query = "SELECT COUNT(*) as total FROM penyakit" . $search_condition;
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total = $total_data['total'];
$pages = ceil($total / $per_page);

// Query data dengan pagination dan pencarian
$query = "SELECT * FROM penyakit" . $search_condition . " ORDER BY kode_penyakit LIMIT $start, $per_page";
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-disease me-2"></i> Manajemen Penyakit
                    </h5>
                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#formTambah" aria-expanded="false" aria-controls="formTambah">
                        <i class="fas fa-plus me-1"></i> Tambah Baru
                    </button>
                </div>
                
                <!-- Form Tambah Penyakit (Collapsible) -->
                <div class="collapse" id="formTambah">
                    <div class="card-body">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Kode Penyakit</label>
                                    <input type="text" name="kode" class="form-control" placeholder="P01" required
                                           pattern="P\d{2}" title="Format: P diikuti 2 angka (contoh: P01)">
                                    <small class="text-muted">Format: P diikuti 2 angka (contoh: P01)</small>
                                </div>
                                <div class="col-md-10">
                                    <label class="form-label">Nama Penyakit</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Solusi Penanganan</label>
                                    <textarea name="solusi" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" name="tambah" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Penyakit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i> Daftar Penyakit
                        </h5>
                        <form class="d-flex" method="get" action="">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Cari penyakit..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="penyakit.php" class="btn btn-sm btn-outline-danger ms-2">
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
                                    <th width="100">Kode</th>
                                    <th>Nama Penyakit</th>
                                    <th>Deskripsi</th>
                                    <th>Solusi</th>
                                    <th width="180" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($row['kode_penyakit']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_penyakit']) ?></td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" 
                                             data-bs-toggle="tooltip" data-bs-placement="top" 
                                             title="<?= htmlspecialchars($row['deskripsi']) ?>">
                                            <?= htmlspecialchars(substr($row['deskripsi'], 0, 50)) ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" 
                                             data-bs-toggle="tooltip" data-bs-placement="top" 
                                             title="<?= htmlspecialchars($row['solusi']) ?>">
                                            <?= htmlspecialchars(substr($row['solusi'], 0, 50)) ?>...
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit_penyakit.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?hapus=<?= $row['id'] ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin menghapus penyakit ini?')" 
                                               title="Hapus">
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
                                <a class="page-link" href="?page=<?= $page-1 ?><?= $search_param ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?><?= $search_param ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page+1 ?><?= $search_param ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <div class="display-4 text-muted mb-4">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h5>Tidak ada data penyakit</h5>
                        <p class="text-muted">Silakan tambahkan penyakit baru menggunakan form di atas</p>
                        <?php if (!empty($search)): ?>
                            <a href="penyakit.php" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke semua penyakit
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
</script>

<?php 
require_once(__DIR__ . '/../layout/footer_layout.php'); 
ob_end_flush(); // Akhir output buffering
?>