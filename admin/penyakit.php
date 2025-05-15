<?php
session_start();
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../includes/header.php');

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Tambah penyakit baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $solusi = mysqli_real_escape_string($conn, $_POST['solusi']);
    
    $query = "INSERT INTO penyakit (kode_penyakit, nama_penyakit, deskripsi, solusi) 
              VALUES ('$kode', '$nama', '$deskripsi', '$solusi')";
    mysqli_query($conn, $query);
}

// Hapus penyakit
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM penyakit WHERE id = $id";
    mysqli_query($conn, $query);
    header('Location: penyakit.php');
    exit();
}

// Ambil semua penyakit
$query = "SELECT * FROM penyakit ORDER BY kode_penyakit";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <h2>Manajemen Penyakit</h2>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Tambah Penyakit Baru</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Kode Penyakit</label>
                        <input type="text" name="kode" class="form-control" placeholder="P01" required>
                    </div>
                    <div class="form-group col-md-10">
                        <label>Nama Penyakit</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Solusi Penanganan</label>
                    <textarea name="solusi" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="tambah" class="btn btn-primary">Tambah Penyakit</button>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Daftar Penyakit</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Penyakit</th>
                            <th>Deskripsi</th>
                            <th>Solusi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['kode_penyakit'] ?></td>
                            <td><?= $row['nama_penyakit'] ?></td>
                            <td><?= substr($row['deskripsi'], 0, 100) ?>...</td>
                            <td><?= substr($row['solusi'], 0, 100) ?>...</td>
                            <td>
                                <a href="edit_penyakit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus penyakit ini?')">Hapus</a>
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
require_once(__DIR__ . '/../includes/footer.php'); 
?>