<?php
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

// Tambah penyakit baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $solusi = mysqli_real_escape_string($conn, $_POST['solusi']);
    
    $query = "INSERT INTO penyakit (kode_penyakit, nama_penyakit, deskripsi, solusi) 
              VALUES ('$kode', '$nama', '$deskripsi', '$solusi')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Penyakit berhasil ditambahkan'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Gagal menambahkan penyakit: ' . mysqli_error($conn)
        ];
    }
    header('Location: penyakit.php');
    exit();
}

// Hapus penyakit
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    // Cek apakah penyakit digunakan di aturan
    $check_query = "SELECT COUNT(*) as total FROM aturan WHERE penyakit_id = $id";
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['total'] > 0) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Tidak dapat menghapus karena penyakit digunakan dalam aturan'
        ];
    } else {
        $query = "DELETE FROM penyakit WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Penyakit berhasil dihapus'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal menghapus penyakit: ' . mysqli_error($conn)
            ];
        }
    }
    header('Location: penyakit.php');
    exit();
}

// Ambil semua penyakit dengan pagination
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM penyakit";
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total = $total_data['total'];
$pages = ceil($total / $per_page);

// Query data dengan pagination
$query = "SELECT * FROM penyakit ORDER BY kode_penyakit LIMIT $start, $per_page";
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
                                    <input type="text" name="kode" class="form-control" placeholder="P01" required>
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
                        <div class="d-flex">
                            <form class="d-flex me-2" method="get" action="">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Cari penyakit..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
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
                                    <th width="150" class="text-center">Aksi</th>
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
                                        <small class="text-muted"><?= substr(htmlspecialchars($row['deskripsi']), 0, 50) ?>...</small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= substr(htmlspecialchars($row['solusi']), 0, 50) ?>...</small>
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
                                            <a href="edit_aturan.php?penyakit_id=<?= $row['id'] ?>" 
                                               class="btn btn-outline-info" title="Aturan">
                                                <i class="fas fa-project-diagram"></i>
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
                        <h5>Tidak ada data penyakit</h5>
                        <p class="text-muted">Silakan tambahkan penyakit baru menggunakan form di atas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
?>