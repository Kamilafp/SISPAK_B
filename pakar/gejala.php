<?php
ob_start(); // Mulai output buffering untuk menghindari header issues
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/layout/header_layout.php');

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

// Fungsi untuk generate kode gejala berikutnya
function getNextGejalaCode($conn) {
    $query = "SELECT kode_gejala FROM gejala ORDER BY kode_gejala DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $last_code = mysqli_fetch_assoc($result)['kode_gejala'];
        // Extract number from code (G01 → 1)
        $last_num = (int) substr($last_code, 1);
        $next_num = $last_num + 1;
    } else {
        // Jika tabel kosong, mulai dari 1
        $next_num = 1;
    }
    
    // Format dengan leading zero (1 → 01)
    return 'G' . str_pad($next_num, 2, '0', STR_PAD_LEFT);
}

$next_code = getNextGejalaCode($conn);

// Tambah gejala baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    
    $query = "INSERT INTO gejala (kode_gejala, nama_gejala) VALUES ('$kode', '$nama')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Gejala berhasil ditambahkan'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Gagal menambahkan gejala: ' . mysqli_error($conn)
        ];
    }
    header('Location: gejala.php');
    exit();
}

// Hapus gejala
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Cek apakah gejala digunakan di aturan
    $check_query = "SELECT COUNT(*) as total FROM aturan WHERE gejala_id = $id";
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['total'] > 0) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Tidak dapat menghapus karena gejala digunakan dalam aturan'
        ];
    } else {
        $query = "DELETE FROM gejala WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Gejala berhasil dihapus'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal menghapus gejala: ' . mysqli_error($conn)
            ];
        }
    }
    header('Location: gejala.php');
    exit();
}

// Ambil semua gejala dengan pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM gejala";
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total = $total_data['total'];
$pages = ceil($total / $per_page);

// Query data dengan pagination
$query = "SELECT * FROM gejala ORDER BY kode_gejala LIMIT $start, $per_page";
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
                        <i class="fas fa-clipboard-list me-2"></i> Manajemen Gejala
                    </h5>
                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#formTambah" aria-expanded="false" aria-controls="formTambah">
                        <i class="fas fa-plus me-1"></i> Tambah Baru
                    </button>
                </div>
                
                <!-- Form Tambah Gejala (Collapsible) -->
                <div class="collapse" id="formTambah">
                    <div class="card-body">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Kode Gejala</label>
                                    <input type="text" name="kode" class="form-control" 
                                        value="<?= htmlspecialchars($next_code) ?>" 
                                        required>
                                </div>
                                <div class="col-md-10">
                                    <label class="form-label">Nama Gejala</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" name="tambah" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Gejala
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
                            <i class="fas fa-list-ul me-2"></i> Daftar Gejala
                        </h5>
                        <form class="d-flex" method="get" action="">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Cari gejala..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-search"></i>
                            </button>
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
                                    <th>Nama Gejala</th>
                                    <th width="180" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($row['kode_gejala']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_gejala']) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit_gejala.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?hapus=<?= $row['id'] ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin menghapus gejala ini?')" 
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
                                <a class="page-link" href="?page=<?= $page-1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page+1 ?>">
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
                        <h5>Tidak ada data gejala</h5>
                        <p class="text-muted">Silakan tambahkan gejala baru menggunakan form di atas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php');
ob_end_flush(); // Akhir output buffering
?>