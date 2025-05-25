<?php
$page_title = "Riwayat Diagnosis";
require_once(__DIR__ . '/layout/header_layout.php');

if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: /../../login.php');
    exit();
}

// Pagination settings
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = $search ? "AND (u.nama LIKE '%$search%' OR p.nama_penyakit LIKE '%$search%')" : '';

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM riwayat r
                JOIN users u ON r.user_id = u.id
                LEFT JOIN penyakit p ON r.penyakit_id = p.id
                WHERE 1 $search_condition";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total = $total_row['total'];
$pages = ceil($total / $per_page);

// Get records for current page
$query = "SELECT r.*, u.nama, p.nama_penyakit 
          FROM riwayat r
          JOIN users u ON r.user_id = u.id
          LEFT JOIN penyakit p ON r.penyakit_id = p.id
          WHERE 1 $search_condition
          ORDER BY r.tanggal DESC
          LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);
?>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Riwayat Diagnosis
            </h5>
            <div class="d-flex">
                <form method="get" class="me-2">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari..." 
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
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Pengguna</th>
                            <th>Hasil Diagnosis</th>
                            <th>Gejala</th>
                            <th>Tanggal</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $start + 1;
                        while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <?= htmlspecialchars($row['nama']) ?>
                            </td>
                            <td>
                                <?php if(!empty($row['nama_penyakit'])): ?>
                                    <span class="badge bg-success">
                                        <?= htmlspecialchars($row['nama_penyakit']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tidak Terdiagnosis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= substr(htmlspecialchars($row['gejala']), 0, 50) ?>...</small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                </small>
                            </td>
                            <td>
                                <a href="riwayat_detail.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger" 
                                   onclick="confirmDelete(<?= $row['id'] ?>)" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
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
                <i class="fas fa-inbox fa-4x mb-4"></i>
                <h5>Tidak ada riwayat diagnosis</h5>
                <p>Belum ada pengguna yang melakukan diagnosis</p>
            </div>
        <?php endif; ?>
    </div>
</div>


<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus riwayat ini?')) {
        window.location.href = 'riwayat_hapus.php?id=' + id;
    }
}
</script>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php'); 
?>