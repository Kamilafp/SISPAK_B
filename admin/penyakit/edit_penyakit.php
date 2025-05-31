<?php
ob_start(); // Mulai output buffering
$page_title = "Edit Penyakit";
require_once(__DIR__ . '/../../includes/functions.php');
require_once(__DIR__ . '/../layout/header_layout.php');

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek login dan role
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

if (!isset($_GET['id'])) {
    header('Location: penyakit.php');
    exit();
}

// ambil data penyakit
$id = (int) $_GET['id'];
$query = "select * from penyakit where id=$id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Data penyakit tidak ditemukan'
    ];
    header('Location: penyakit.php');
    exit();
}

// update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $solusi = mysqli_real_escape_string($conn, $_POST['solusi']);

    //cek duplikasi
    $cek_duplikasi = "select id from penyakit where kode_penyakit = '$kode' and id!=$id";
    $hasil_duplikasi = mysqli_query($conn, $cek_duplikasi);

    if(mysqli_num_rows($hasil_duplikasi)>0){
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Kode Penyakit sudah digunakan. Silahkan gunakan Kode lain.'
        ];
        header("Location: edit_penyakit.php?id=$id");
        exit();
    }else{
        $update = "update penyakit set
        kode_penyakit = '$kode',
        nama_penyakit = '$nama',
        deskripsi = '$deskripsi',
        solusi = '$solusi'
        where id = $id";
        if (mysqli_query($conn, $update)) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Penyakit berhasil diubah'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal mengubah penyakit: ' . mysqli_error($conn)
            ];
        }
        // mengarahkan ke baris data stlh berhasil ubah data
        if (mysqli_query($conn, $update)) {
            // ambil smua data penyakit utk menghitung posisi
            $get_all = 'select id from penyakit order by kode_penyakit';
            $get_result = mysqli_query($conn, $get_all);

            $position = 0;
            while ($row = mysqli_fetch_assoc($get_result)) {
                $position++;
                if ($row['id'] == $id) {
                    break;
                }
            }
            $per_page = 10; //hrs sm ky yg di penyakit
            // hitung halaman berdasarkan posisi
            $page_target = ceil($position / $per_page);

            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Penyakit berhasil diubah'
            ];

            // redirect ke halaman dan scroll ke baris penyakit tsb
            header("Location: penyakit.php?page=$page_target#penyakit-$id");
            exit();
        }
    }
}
?>

<div class="row">
    <div class="col-12">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-disease me-2"></i> Manajemen Penyakit
                </h5>
                <div class="">
                    <i class="fas fa-edit"></i>
                    Edit Data
                </div>
            </div>

            <!-- Form Edit Penyakit -->
            <div class="card-body">
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Kode Penyakit</label>
                            <input type="text" name="kode" class="form-control" value="<?= ($data['kode_penyakit']) ?>"
                                required>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label">Nama Penyakit</label>
                            <input type="text" name="nama" class="form-control" required
                                value="<?= ($data['nama_penyakit']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"
                                required><?= ($data['deskripsi']) ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Solusi Penanganan</label>
                            <textarea name="solusi" class="form-control" rows="3"
                                required><?= ($data['solusi']) ?></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" name="edit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Penyakit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>