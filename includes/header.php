<?php
require_once(__DIR__ . '/../config/database.php');
define('BASEPATH', '/SISPAK_B/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Diagnosis Penyakit Gigi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image" href="<?php echo BASEPATH; ?>assets/img/favicon.png">
    <style>
        .navbar {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            color: #2c3e50 !important;
        }
        
        .navbar-brand img {
            margin-right: 10px;
        }
        
        .nav-item {
            margin: 0 5px;
            font-weight: 500;
        }
        
        .nav-link {
            color: #2c3e50 !important;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .nav-link i {
            margin-right: 8px;
            font-size: 0.9em;
            color: #3498db;
        }
        
        .nav-link:hover {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db !important;
        }
        
        .nav-link:hover i {
            color: #2980b9;
        }
        
        .navbar-toggler {
            border-color: rgba(44, 62, 80, 0.1);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='rgba(44, 62, 80, 0.8)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .active-nav {
            background-color: rgba(52, 152, 219, 0.15);
            color: #3498db !important;
            font-weight: 600;
        }
        
        @media (max-width: 991.98px) {
            .navbar {
                background-color: rgba(255, 255, 255, 0.98) !important;
            }
            
            .nav-item {
                margin: 5px 0;
            }
            
            .nav-link {
                padding: 0.8rem 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/SISPAK_B/index.php">
                <i class="fas fa-tooth" style="color: #3498db;"></i>&nbsp;
                <span>Sistem Pakar Penyakit Gigi</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role']=='admin') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/SISPAK_B/admin/dashboard.php">
                                    <span>Admin</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/SISPAK_B/dashboard.php">
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SISPAK_B/konsultasi.php">
                                <span>Konsultasi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SISPAK_B/logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/SISPAK_B/index.php">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SISPAK_B/login.php">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        // Efek scroll navbar
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });
    </script>