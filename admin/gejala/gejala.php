<?php
ob_start();
$page_title = "Manajemen Gejala";
require_once(__DIR__ . '/../../includes/functions.php');
require_once(__DIR__ . '/../layout/header_layout.php');

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek login dan role
if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'pakar') {
    header('Location: ' . BASE_PATH . 'login.php');
    exit();
}

// Handle flash messages
if (isset($_SESSION['flash_message'])) {
    $flash_type = $_SESSION['flash_message']['type'];
    $flash_msg = $_SESSION['flash_message']['message'];
    unset($_SESSION['flash_message']);
}

// Tambah gejala baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    
    // Validasi format kode gejala (contoh: G01)
    if (!preg_match('/^G\d{2}$/', $kode)) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Format kode gejala harus G diikuti 2 angka (contoh: G01)'
        ];
        header('Location: gejala.php');
        exit();
    }
    
    // Gunakan prepared statement
    $stmt = $conn->prepare("INSERT INTO gejala (kode_gejala, nama_gejala) VALUES (?, ?)");
    $stmt->bind_param("ss", $kode, $nama);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Gejala berhasil ditambahkan'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Gagal menambahkan gejala: ' . $stmt->error
        ];
    }
    $stmt->close();
    header('Location: gejala.php');
    exit();
}

// Hapus gejala
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    if ($id <= 0) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'ID gejala tidak valid'
        ];
        header('Location: gejala.php');
        exit();
    }
    
    // Ambil data gejala untuk pesan konfirmasi
    $query = "SELECT kode_gejala, nama_gejala FROM gejala WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    if (!$data) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Gejala tidak ditemukan'
        ];
        header('Location: gejala.php');
        exit();
    }
    
    // Cek apakah gejala digunakan di aturan
    $check_query = "SELECT COUNT(*) as total FROM aturan WHERE gejala_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $check_result = $stmt->get_result();
    $check_data = $check_result->fetch_assoc();
    $stmt->close();
    
    if ($check_data['total'] > 0) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Tidak dapat menghapus karena gejala digunakan dalam aturan'
        ];
    } else {
        $stmt = $conn->prepare("DELETE FROM gejala WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Gejala "' . $data['kode_gejala'] . ' - ' . $data['nama_gejala'] . '" berhasil dihapus'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal menghapus gejala: ' . $stmt->error
            ];
        }
        $stmt->close();
    }
    
    // Redirect dengan mempertahankan parameter pencarian dan pagination
    $redirect_url = 'gejala.php';
    if (isset($_GET['page'])) {
        $redirect_url .= '?page=' . (int)$_GET['page'];
    }
    if (!empty($_GET['search'])) {
        $redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&');
        $redirect_url .= 'search=' . urlencode($_GET['search']);
    }
    $redirect_url .= '#gejala-list';
    
    header('Location: ' . $redirect_url);
    exit();
}

// Konfigurasi pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Pencarian gejala
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_param = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = " WHERE nama_gejala LIKE '%$search%' OR kode_gejala LIKE '%$search%'";
    $search_param = '&search=' . urlencode($search);
}

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM gejala" . $search_condition;
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total = $total_data['total'];
$pages = ceil($total / $per_page);

// Query data
$query = "SELECT * FROM gejala" . $search_condition . " ORDER BY kode_gejala LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid py-4">
    <!-- Flash Message -->
    <?php if (isset($flash_type) && isset($flash_msg)): ?>
    <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas <?= ($flash_type == 'success') ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i>
            <div><?= $flash_msg ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
    </script>
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
                
                <!-- Form Tambah Gejala -->
                <div class="collapse" id="formTambah">
                    <div class="card-body">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Kode Gejala</label>
                                    <?php
                                    // Generate kode otomatis
                                    $last_code = 'G00';
                                    $kode_query = "SELECT kode_gejala FROM gejala ORDER BY kode_gejala DESC LIMIT 1";
                                    $kode_result = mysqli_query($conn, $kode_query);
                                    if ($kode_result && mysqli_num_rows($kode_result) > 0) {
                                        $kode_row = mysqli_fetch_assoc($kode_result);
                                        $last_code = $kode_row['kode_gejala'];
                                    }
                                    $next_number = (int)substr($last_code, 1) + 1;
                                    $next_code = 'G' . str_pad($next_number, 2, '0', STR_PAD_LEFT);
                                    ?>
                                    <input type="text" name="kode" class="form-control" placeholder="G01" required
                                           pattern="G\d{2}" title="Format: G diikuti 2 angka (contoh: G01)" 
                                           value="<?= htmlspecialchars($next_code) ?>" readonly>
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

    <div class="row" id="gejala-list">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list-ul me-2"></i> Daftar Gejala
                        </h5>
                        <form class="d-flex" method="get" action="">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Cari gejala..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="gejala.php" class="btn btn-sm btn-outline-danger ms-2">
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
                                    <th>Nama Gejala</th>
                                    <th width="180" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr id="gejala-<?= $row['id']?>">
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
                                            <button type="button" class="btn btn-outline-danger" 
                                               onclick="confirmDelete(<?= $row['id'] ?>)" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                        <h5>Tidak ada data gejala</h5>
                        <p class="text-muted">Silakan tambahkan gejala baru menggunakan form di atas</p>
                        <?php if (!empty($search)): ?>
                            <a href="gejala.php" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke semua gejala
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
// Konfirmasi hapus dengan SweetAlert2
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Gejala ini akan dihapus permanen dari sistem!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        backdrop: `
            rgba(0,0,0,0.7)
            url("<?= BASE_PATH ?>assets/images/trash-icon.png")
            center top
            no-repeat
        `
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect dengan mempertahankan parameter pencarian dan pagination
            let url = 'gejala.php?hapus=' + id;
            const search = new URLSearchParams(window.location.search).get('search');
            const page = new URLSearchParams(window.location.search).get('page');
            
            if (page) url += '&page=' + page;
            if (search) url += '&search=' + encodeURIComponent(search);
            url += '#gejala-list';
            
            window.location.href = url;
        }
    });
}

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
ob_end_flush();
?>