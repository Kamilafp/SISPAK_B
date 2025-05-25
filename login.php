<?php
session_start();
require_once 'config/database.php';
require_once(__DIR__ . '/includes/functions.php');
require_once(__DIR__ . '/includes/header.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            if ($user['is_admin'] == 1) {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Email tidak ditemukan!';
    }
}
?>

<style>
    .login-container {
        max-width: 500px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    .login-header {
        color: #2c3e50;
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .login-form input {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 50px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .login-form input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }
    
    .login-btn {
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
    
    .login-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .login-footer {
        margin-top: 1.5rem;
        color: #7f8c8d;
    }
    
    .login-footer a {
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
</style>

<div class="login-container">
    <h2 class="login-header">Masuk ke Akun Anda</h2>
    
    <?php if ($error): ?>
        <div class="alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="post" class="login-form">
        <input type="email" name="email" placeholder="Alamat Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="login-btn">Masuk</button>
    </form>
    
    <div class="login-footer">
        Belum punya akun? <a href="register.php">Daftar Sekarang</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>