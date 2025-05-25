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
                $success = 'Pendaftaran berhasil! Anda akan dialihkan ke halaman login...';
                header('refresh:2;url=login.php');
            } else {
                $error = 'Terjadi kesalahan: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<style>
    .register-container {
        max-width: 500px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    .register-header {
        color: #2c3e50;
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .register-form input {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 50px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .register-form input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }
    
    .register-btn {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 1rem;
    }
    
    .register-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .register-footer {
        margin-top: 1.5rem;
        color: #7f8c8d;
    }
    
    .register-footer a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }
    
    .alert-danger {
        background-color: #e74c3c;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background-color: #2ecc71;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 1rem;
    }
</style>

<div class="register-container">
    <h2 class="register-header">Daftar Akun Baru</h2>
    
    <?php if ($error): ?>
        <div class="alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert-success"><?= $success ?></div>
    <?php else: ?>
        <form method="post" class="register-form">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Alamat Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <button type="submit" class="register-btn">Daftar Sekarang</button>
        </form>
    <?php endif; ?>
    
    <div class="register-footer">
        Sudah punya akun? <a href="login.php">Masuk disini</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>