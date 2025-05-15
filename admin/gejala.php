<?php
session_start();
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../includes/header.php');

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Tambah gejala baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    
    $query = "INSERT INTO gejala (kode_gejala, nama_gejala) VALUES ('$kode', '$nama')";
    mysqli_query($conn, $query);
}

// Hapus gejala
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM gejala WHERE id = $id";
    mysqli_query($conn, $query);
    header('Location: gejala.php');
    exit();
}

// Ambil semua gejala
$query = "SELECT * FROM gejala ORDER BY kode_gejala";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <h2>Manajemen Gejala</h2>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Tambah Gejala Baru</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Kode Gejala</label>
                        <input type="text" name="kode" class="form-control" placeholder="G01" required>
                    </div>
                    <div class="form-group col-md-10">
                        <label>Nama Gejala</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-primary">Tambah Gejala</button>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Daftar Gejala</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Gejala</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['kode_gejala'] ?></td>
                            <td><?= $row['nama_gejala'] ?></td>
                            <td>
                                <a href="edit_gejala.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus gejala ini?')">Hapus</a>
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