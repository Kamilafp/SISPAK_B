<?php
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/layout/header_layout.php');

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Tambah aturan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $penyakit_id = mysqli_real_escape_string($conn, $_POST['penyakit_id']);
    $gejala_id = mysqli_real_escape_string($conn, $_POST['gejala_id']);
    
    // Cek apakah aturan sudah ada
    $check_query = "SELECT * FROM aturan WHERE penyakit_id = $penyakit_id AND gejala_id = $gejala_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        $query = "INSERT INTO aturan (penyakit_id, gejala_id) VALUES ($penyakit_id, $gejala_id)";
        mysqli_query($conn, $query);
    }
}

// Hapus aturan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM aturan WHERE id = $id";
    mysqli_query($conn, $query);
    header('Location: aturan.php');
    exit();
}

// Ambil semua aturan dengan join ke penyakit dan gejala
$query = "SELECT a.id, p.kode_penyakit, p.nama_penyakit, g.kode_gejala, g.nama_gejala 
          FROM aturan a
          JOIN penyakit p ON a.penyakit_id = p.id
          JOIN gejala g ON a.gejala_id = g.id
          ORDER BY p.kode_penyakit, g.kode_gejala";
$result = mysqli_query($conn, $query);

// Ambil semua penyakit untuk dropdown
$penyakit_query = "SELECT * FROM penyakit ORDER BY kode_penyakit";
$penyakit_result = mysqli_query($conn, $penyakit_query);

// Ambil semua gejala untuk dropdown
$gejala_query = "SELECT * FROM gejala ORDER BY kode_gejala";
$gejala_result = mysqli_query($conn, $gejala_query);
?>

<div class="container mt-5">
    <h2>Manajemen Aturan</h2>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Tambah Aturan Baru</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Penyakit</label>
                        <select name="penyakit_id" class="form-control" required>
                            <option value="">Pilih Penyakit</option>
                            <?php while ($penyakit = mysqli_fetch_assoc($penyakit_result)): ?>
                            <option value="<?= $penyakit['id'] ?>">
                                <?= $penyakit['kode_penyakit'] ?> - <?= $penyakit['nama_penyakit'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Gejala</label>
                        <select name="gejala_id" class="form-control" required>
                            <option value="">Pilih Gejala</option>
                            <?php while ($gejala = mysqli_fetch_assoc($gejala_result)): ?>
                            <option value="<?= $gejala['id'] ?>">
                                <?= $gejala['kode_gejala'] ?> - <?= $gejala['nama_gejala'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-primary">Tambah Aturan</button>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Daftar Aturan</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Penyakit</th>
                            <th>Gejala</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['kode_penyakit'] ?> - <?= $row['nama_penyakit'] ?></td>
                            <td><?= $row['kode_gejala'] ?> - <?= $row['nama_gejala'] ?></td>
                            <td>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus aturan ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/layout/footer_layout.php');
?>