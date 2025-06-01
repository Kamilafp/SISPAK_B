<?php
ob_start();
$page_title = "Edit Aturan";
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

// ambil data aturan
$id = (int)$_GET['id'];
$query = "select * from aturan where id=$id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if(!$data){
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Data aturan tidak ditemukan'
    ];
    header('Location: aturan.php');
    exit();
}

// edit aturan
if ($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_POST['edit'])) {
    $penyakit_id = mysqli_real_escape_string($conn, $_POST['penyakit_id']);
    $gejala_id = mysqli_real_escape_string($conn, $_POST['gejala_id']);
    
    // Cek apakah aturan sudah ada
    $check_query = "SELECT * FROM aturan WHERE penyakit_id = $penyakit_id AND gejala_id = $gejala_id and id!=$id";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result)>0){
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Aturan sudah ada. Silahkan buat Aturan lain.'
        ];
        header("Location: edit_aturan.php?id=$id");
        exit();
    }else{
        $update = "update aturan set
        penyakit_id = '$penyakit_id',
        gejala_id = '$gejala_id'
        where id = $id";
        if (mysqli_query($conn, $update)) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Aturan berhasil diubah'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Gagal mengubah aturan: ' . mysqli_error($conn)
            ];
        }
        // mengarahkan ke baris data stlh berhasil ubah data
        if (mysqli_query($conn, $update)) {
            // ambil smua data gejala utk menghitung posisi
            $get_all = 'select id from aturan order by id';
            $get_result = mysqli_query($conn, $get_all);

            $position = 0;
            while ($row = mysqli_fetch_assoc($get_result)) {
                $position++;
                if ($row['id'] == $id) {
                    break;
                }
            }
            $per_page = 10; //hrs sm ky yg di aturan
            // hitung halaman berdasarkan posisi
            $page_target = ceil($position / $per_page);

            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Aturan berhasil diubah'
            ];

            // redirect ke halaman dan scroll ke baris aturan tsb
            header("Location: aturan.php?page=$page_target#aturan-$id");
            exit();
        }
    }
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

<div class="row">
    <div class="col-12">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i> Manajemen Aturan
                </h5>
                <div>
                    <i class="fas fa-edit"></i> Edit Aturan
                </div>
            </div>
        <!-- form edit aturan -->
        <div class="card-body">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Penyakit</label>
                        <select name="penyakit_id" class="form-control" required>
                            <option value="">Pilih Penyakit</option>
                            <?php while ($penyakit = mysqli_fetch_assoc($penyakit_result)): ?>
                            <option value="<?= $penyakit['id'] ?>" <?= $penyakit['id'] == $data['penyakit_id'] ? 'selected' : ''?>>
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
                            <option value="<?= $gejala['id'] ?>" <?= $gejala['id'] == $data['gejala_id'] ? 'selected' : ''?>>
                                <?= $gejala['kode_gejala'] ?> - <?= $gejala['nama_gejala'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 text-end mt-3">
                    <button type="submit" name="edit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Aturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once(__DIR__ . '/../layout/footer_layout.php');
?>