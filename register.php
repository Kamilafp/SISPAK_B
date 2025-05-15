<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validasi
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } else {
        // Cek apakah email sudah terdaftar
        $check_email = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        
        if (mysqli_num_rows($result) > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru
            $query = "INSERT INTO users (nama, email, password) VALUES ('$nama', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $query)) {
                $success = 'Pendaftaran berhasil! Silakan login.';
                header('refresh:2;url=login.php');
            } else {
                $error = 'Terjadi kesalahan: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Pendaftaran Akun</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php else: ?>
                        <form method="post">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    Sudah punya akun? <a href="login.php">Login disini</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>