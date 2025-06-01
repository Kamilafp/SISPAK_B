<?php
ob_start();
$page_title = "Edit Pengguna";
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

// Ambil ID pengguna dari URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'ID pengguna tidak valid'
    ];
    header('Location: pengguna.php');
    exit();
}

// Ambil data pengguna
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Pengguna tidak ditemukan'
    ];
    header('Location: pengguna.php');
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $role = $_POST['role'];
    
    // Validasi input
    $errors = [];
    
    if (!in_array($role, ['admin', 'pakar', 'user'])) {
        $errors[] = "Role tidak valid";
    }
    if (empty($errors)) {
        // Update data
            $query = "UPDATE users SET role = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $role, $user_id);
        } else {
            // Jika password tidak diubah
            $query = "UPDATE users SET role = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $role, $user_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Data pengguna berhasil diperbarui'
            ];
            header('Location: pengguna.php');
            exit();
        } else {
            $errors[] = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => implode("<br>", $errors)
        ];
    }
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
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i> Edit Pengguna
                        </h5>
                        <a href="pengguna.php" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" 
                                       value="<?= htmlspecialchars($user['nama']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="pakar" <?= $user['role'] == 'pakar' ? 'selected' : '' ?>>Pakar</option>
                                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            Terdaftar pada: <?= date('d F Y H:i', strtotime($user['created_at'])) ?>
                                        </small>
                                    </div>
                                    <button type="submit" name="update" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    
    // Scroll ke flash message jika ada
    const flashMsg = document.querySelector('.alert');
    if (flashMsg) {
        flashMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

<?php 
require_once(__DIR__ . '/../layout/footer_layout.php');
ob_end_flush();
?>