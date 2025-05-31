<?php
ob_start();
$page_title = "Edit Gejala";
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


if(!isset($_GET['id'])){
    header('Location: gejala.php');
    exit();
}

// ambil data gejala
$id = (int)$_GET['id'];
$query = "select * from gejala where id=$id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if(!$data){
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Data gejala tidak ditemukan'
    ];
    header('Location: gejala.php');
    exit();
}

// edit gejala
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    //cek duplikasi
    $cek_duplikasi = "select id from gejala where kode_gejala = '$kode' and id!=$id";
    $hasil_duplikasi = mysqli_query($conn, $cek_duplikasi);

    if(mysqli_num_rows($hasil_duplikasi)>0){
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Kode Gejala sudah digunakan. Silahkan gunakan Kode lain.'
        ];
        header("Location: edit_gejala.php?id=$id");
        exit();
    }else{
        $update = "update gejala set
        kode_gejala = '$kode',
        nama_gejala = '$nama'
        where id = $id";
        if (mysqli_query($conn, $update)) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Gejala berhasil diubah'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal mengubah gejala: ' . mysqli_error($conn)
            ];
        }
        // mengarahkan ke baris data stlh berhasil ubah data
        if (mysqli_query($conn, $update)) {
            // ambil smua data gejala utk menghitung posisi
            $get_all = 'select id from gejala order by kode_gejala';
            $get_result = mysqli_query($conn, $get_all);

            $position = 0;
            while ($row = mysqli_fetch_assoc($get_result)) {
                $position++;
                if ($row['id'] == $id) {
                    break;
                }
            }
            $per_page = 10; //hrs sm ky yg di gejala
            // hitung halaman berdasarkan posisi
            $page_target = ceil($position / $per_page);

            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Gejala berhasil diubah'
            ];

            // redirect ke halaman dan scroll ke baris gejala tsb
            header("Location: gejala.php?page=$page_target#gejala-$id");
            exit();
        }
    }
}
?>
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
                    <i class="fas fa-clipboard-list me-2"></i> Manajemen Gejala
                </h5>
                <div>
                    <i class="fas fa-edit"></i> Edit Gejala
                </div>
            </div>
            
        <!-- Form Edit Gejala -->
            <div class="card-body">
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Kode Gejala</label>
                            <input type="text" name="kode" class="form-control" 
                                value="<?=($data['kode_gejala'])?>" 
                                required>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label">Nama Gejala</label>
                            <input type="text" name="nama" class="form-control" required value="<?=($data['nama_gejala'])?>">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" name="edit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Gejala
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>