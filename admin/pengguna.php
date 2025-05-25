<?php
$page_title = "Manajemen Pengguna";
require_once(__DIR__ . '/layout/header_layout.php');

if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: /../../login.php');
    exit();
}

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    
    // Prevent admin from deleting themselves
    if ($delete_id != $_SESSION['user_id']) {
        $delete_query = "DELETE FROM users WHERE id = $delete_id";
        mysqli_query($conn, $delete_query);
        
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Pengguna berhasil dihapus'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Tidak dapat menghapus akun sendiri'
        ];
    }
    
    header('Location: user.php');
    exit();
}

// Pagination settings
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = $search ? "WHERE (nama LIKE '%$search%' OR email LIKE '%$search%')" : '';

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM users $search_condition";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total = $total_row['total'];
$pages = ceil($total / $per_page);

// Get records for current page
$query = "SELECT * FROM users $search_condition ORDER BY nama ASC LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);
?>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>Manajemen Pengguna
            </h5>
            <div class="d-flex">
                <a href="user_tambah.php" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus me-1"></i> Tambah Pengguna
                </a>
                <form method="get" class="me-2">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari pengguna..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['flash_message']['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
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
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <?php if($row['role'] == 'admin'): ?>
                                    <span class="badge bg-primary">Admin</span>
                                <?php elseif ($row['role']=='pakar'): ?>
                                    <span class="badge bg-success">Pakar</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <a href="user_edit.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" 
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak dapat menghapus akun sendiri">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= $search ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php for($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= $search ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-user-slash fa-4x mb-4"></i>
                <h5>Tidak ada data pengguna</h5>
                <p>Belum ada pengguna yang terdaftar</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
?>