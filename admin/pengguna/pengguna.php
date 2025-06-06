<?php
ob_start();
$page_title = "Manajemen Pengguna";
require_once(__DIR__ . '/../../includes/functions.php');
require_once(__DIR__ . '/../layout/header_layout.php');

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek login dan role admin
if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ' . BASE_PATH . 'login.php');
    exit();
}

// Handle flash messages
if (isset($_SESSION['flash_message'])) {
    $flash_type = $_SESSION['flash_message']['type'];
    $flash_msg = $_SESSION['flash_message']['message'];
    unset($_SESSION['flash_message']);
}

// Handle tambah pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validasi input
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = "Nama harus diisi";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    if (!in_array($role, ['admin', 'pakar', 'user'])) {
        $errors[] = "Role tidak valid";
    }
    
    // Cek email sudah ada
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        $errors[] = "Email sudah terdaftar";
    }
    $check_email->close();
    
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Pengguna berhasil ditambahkan'
            ];
            header('Location: pengguna.php');
            exit();
        } else {
            $errors[] = "Gagal menambahkan pengguna: " . $stmt->error;
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => implode("<br>", $errors)
        ];
    }
}

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    
    if ($delete_id <= 0) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'ID pengguna tidak valid'
        ];
        header('Location: pengguna.php');
        exit();
    }
    
    // Ambil data pengguna untuk pesan konfirmasi
    $query = "SELECT nama, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();
    
    // Prevent admin from deleting themselves
    if ($delete_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Pengguna "' . $user_data['nama'] . ' (' . $user_data['email'] . ')" berhasil dihapus'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal menghapus pengguna: ' . $stmt->error
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Tidak dapat menghapus akun sendiri'
        ];
    }
    
    // Redirect dengan mempertahankan parameter pencarian dan pagination
    $redirect_url = 'pengguna.php';
    if (isset($_GET['page'])) {
        $redirect_url .= '?page=' . (int)$_GET['page'];
    }
    if (!empty($_GET['search'])) {
        $redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&');
        $redirect_url .= 'search=' . urlencode($_GET['search']);
    }
    $redirect_url .= '#pengguna-list';
    
    header('Location: ' . $redirect_url);
    exit();
}

// Konfigurasi pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Pencarian pengguna
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_param = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = " WHERE (nama LIKE '%$search%' OR email LIKE '%$search%' OR role LIKE '%$search%')";
    $search_param = '&search=' . urlencode($search);
}

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM users" . $search_condition;
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total = $total_data['total'];
$pages = ceil($total / $per_page);

// Query data
$query = "SELECT * FROM users" . $search_condition . " ORDER BY nama ASC LIMIT $start, $per_page";
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
                        <i class="fas fa-users me-2"></i> Manajemen Pengguna
                    </h5>
                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#formTambah" aria-expanded="false" aria-controls="formTambah">
                        <i class="fas fa-plus me-1"></i> Tambah Baru
                    </button>
                </div>
                
                <!-- Form Tambah Pengguna -->
                <div class="collapse" id="formTambah">
                    <div class="card-body">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required minlength="6">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="">Pilih Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="pakar">Pakar</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" name="tambah" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Pengguna
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="pengguna-list">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list-ul me-2"></i> Daftar Pengguna
                        </h5>
                        <form class="d-flex" method="get" action="">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Cari pengguna..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="pengguna.php" class="btn btn-sm btn-outline-danger ms-2">
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
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Tanggal Daftar</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = $start + 1;
                                while($row = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr id="pengguna-<?= $row['id']?>">
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <?php if($row['role'] == "admin"): ?>
                                            <span class="badge bg-success">Admin</span>
                                        <?php elseif($row['role'] == "pakar"): ?>
                                            <span class="badge bg-primary">Pakar</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit_pengguna.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                                <button type="button" class="btn btn-outline-danger" 
                                                   onclick="confirmDelete(<?= $row['id'] ?>)" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary" disabled title="Tidak dapat menghapus akun sendiri">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
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
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h5>Tidak ada data pengguna</h5>
                        <p class="text-muted">Silakan tambahkan pengguna baru menggunakan form di atas</p>
                        <?php if (!empty($search)): ?>
                            <a href="pengguna.php" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke semua pengguna
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
        text: "Pengguna ini akan dihapus permanen dari sistem!",
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
            let url = 'pengguna.php?delete=' + id;
            const search = new URLSearchParams(window.location.search).get('search');
            const page = new URLSearchParams(window.location.search).get('page');
            
            if (page) url += '&page=' + page;
            if (search) url += '&search=' + encodeURIComponent(search);
            url += '#pengguna-list';
            
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