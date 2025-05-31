<?php
ob_start(); // Mulai output buffering
require_once(__DIR__ . '/../../includes/functions.php');
require_once(__DIR__ . '/../layout/header_layout.php');

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek login dan role admin
if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'pakar') {
    header('Location: /SISPAK_B/login.php');
    exit();
}

// Handle flash messages
if (isset($_SESSION['flash_message'])) {
    $flash_type = $_SESSION['flash_message']['type'];
    $flash_msg = $_SESSION['flash_message']['message'];
    unset($_SESSION['flash_message']);
}

// Tambah aturan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $penyakit_id = (int)$_POST['penyakit_id'];
    $gejala_id = (int)$_POST['gejala_id'];
    
    // Validasi input
    if ($penyakit_id <= 0 || $gejala_id <= 0) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Penyakit dan gejala harus dipilih'
        ];
        header('Location: aturan.php');
        exit();
    }
    
    // Cek apakah aturan sudah ada menggunakan prepared statement
    $check_query = "SELECT * FROM aturan WHERE penyakit_id = ? AND gejala_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $penyakit_id, $gejala_id);
    $stmt->execute();
    $check_result = $stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        $insert_query = "INSERT INTO aturan (penyakit_id, gejala_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $penyakit_id, $gejala_id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Aturan berhasil ditambahkan'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal menambahkan aturan: ' . $stmt->error
            ];
        }
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'warning',
            'message' => 'Aturan sudah ada dalam sistem'
        ];
    }
    
    $stmt->close();
    header('Location: aturan.php');
    exit();
}

// Hapus aturan
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM aturan WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Aturan berhasil dihapus'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal menghapus aturan: ' . $stmt->error
            ];
        }
        
        $stmt->close();
    }
    
    header('Location: aturan.php');
    exit();
}

// Konfigurasi pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Pastikan tidak kurang dari 1
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Pencarian aturan
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_param = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = " WHERE p.nama_penyakit LIKE '%$search%' 
                          OR p.kode_penyakit LIKE '%$search%'
                          OR g.nama_gejala LIKE '%$search%'
                          OR g.kode_gejala LIKE '%$search%'";
    $search_param = '&search=' . urlencode($search);
}

// Hitung total data dengan kondisi pencarian
$total_query = "SELECT COUNT(*) as total 
                FROM aturan a
                JOIN penyakit p ON a.penyakit_id = p.id
                JOIN gejala g ON a.gejala_id = g.id" . $search_condition;
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total = $total_data['total'];
$pages = ceil($total / $per_page);

// Query data dengan pagination dan pencarian
$query = "SELECT a.id, p.kode_penyakit, p.nama_penyakit, g.kode_gejala, g.nama_gejala 
          FROM aturan a
          JOIN penyakit p ON a.penyakit_id = p.id
          JOIN gejala g ON a.gejala_id = g.id" . 
          $search_condition . " 
          ORDER BY p.kode_penyakit, g.kode_gejala
          LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);

// Ambil semua penyakit untuk dropdown
$penyakit_query = "SELECT * FROM penyakit ORDER BY kode_penyakit";
$penyakit_result = mysqli_query($conn, $penyakit_query);

// Ambil semua gejala untuk dropdown
$gejala_query = "SELECT * FROM gejala ORDER BY kode_gejala";
$gejala_result = mysqli_query($conn, $gejala_query);
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
                        <i class="fas fa-project-diagram me-2"></i> Manajemen Aturan
                    </h5>
                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#formTambah" aria-expanded="false" aria-controls="formTambah">
                        <i class="fas fa-plus me-1"></i> Tambah Baru
                    </button>
                </div>
                
                <!-- Form Tambah Aturan (Collapsible) -->
                <div class="collapse" id="formTambah">
                    <div class="card-body">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Penyakit</label>
                                    <select name="penyakit_id" class="form-select" required>
                                        <option value="">Pilih Penyakit</option>
                                        <?php while ($penyakit = mysqli_fetch_assoc($penyakit_result)): ?>
                                        <option value="<?= $penyakit['id'] ?>">
                                            <?= htmlspecialchars($penyakit['kode_penyakit']) ?> - <?= htmlspecialchars($penyakit['nama_penyakit']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gejala</label>
                                    <select name="gejala_id" class="form-select" required>
                                        <option value="">Pilih Gejala</option>
                                        <?php while ($gejala = mysqli_fetch_assoc($gejala_result)): ?>
                                        <option value="<?= $gejala['id'] ?>">
                                            <?= htmlspecialchars($gejala['kode_gejala']) ?> - <?= htmlspecialchars($gejala['nama_gejala']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" name="tambah" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Aturan
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
                            <i class="fas fa-list-ul me-2"></i> Daftar Aturan
                        </h5>
                        <form class="d-flex" method="get" action="">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Cari aturan..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="aturan.php" class="btn btn-sm btn-outline-danger ms-2">
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
                                    <th>Penyakit</th>
                                    <th>Gejala</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = $start + 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr id="aturan-<?= $row['id']?>">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($row['kode_penyakit']) ?></span>
                                        <?= htmlspecialchars($row['nama_penyakit']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= htmlspecialchars($row['kode_gejala']) ?></span>
                                        <?= htmlspecialchars($row['nama_gejala']) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit_aturan.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?hapus=<?= $row['id'] ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin menghapus aturan ini?')" 
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
                        <h5>Tidak ada data aturan</h5>
                        <p class="text-muted">Silakan tambahkan aturan baru menggunakan form di atas</p>
                        <?php if (!empty($search)): ?>
                            <a href="aturan.php" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke semua aturan
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
document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;
    if (hash && document.querySelector(hash)) {
        const element = document.querySelector(hash);
        element.scrollIntoView({ behavior: "smooth", block: "center" });
    }
});
</script>

<?php
require_once(__DIR__ . '/../layout/footer_layout.php');
ob_end_flush(); // Akhir output buffering
?>