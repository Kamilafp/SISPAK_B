<?php
ob_start();
session_start();
require_once(__DIR__ . '/../../includes/functions.php');

// Define base path
define('BASE_PATH', '/SISPAK_B/');

if (!isLoggedIn()) {
    header('Location: ' . BASE_PATH . 'login.php');
    exit();
}
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'pakar') {
    header('Location: ' . BASE_PATH . 'dashboard.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 56px;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            width: 250px;
            background: #343a40;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1rem;
            margin-bottom: 0.2rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        .navbar-brand {
            padding: 0.5rem 1rem;
        }
        .card-dashboard {
            transition: transform 0.2s;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .sidebar.show {
                width: 250px;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler me-2" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <a class="navbar-brand" href="<?= BASE_PATH ?>admin/dashboard.php">Admin Panel</a>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'pakar') : ?>
                <a class="navbar-brand" href="<?= BASE_PATH ?>admin/dashboard.php">Pakar Panel</a>
            <?php endif; ?>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?= BASE_PATH ?>admin/profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_PATH ?>admin/settings.php"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_PATH ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header p-3">
            <h4 class="text-white">Diagnosa Penyakit</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_PATH ?>admin/dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <?php if ($_SESSION['role'] === 'pakar') : ?>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'penyakit.php' ? 'active' : '' ?>" href="<?= BASE_PATH ?>admin/penyakit/penyakit.php">
                    <i class="fas fa-disease"></i> Kelola Penyakit
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'gejala.php' ? 'active' : '' ?>" href="<?= BASE_PATH ?>admin/gejala/gejala.php">
                    <i class="fas fa-clipboard-list"></i> Kelola Gejala
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'aturan.php' ? 'active' : '' ?>" href="<?= BASE_PATH ?>admin/aturan/aturan.php">
                    <i class="fas fa-project-diagram"></i> Kelola Aturan
                </a>
            </li>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'admin') : ?>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pengguna.php' ? 'active' : '' ?>" href="<?= BASE_PATH ?>admin/pengguna/pengguna.php">
                    <i class="fas fa-users"></i> Kelola Pengguna
                </a>
            </li>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'pakar') : ?>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'riwayat.php' ? 'active' : '' ?>" href="<?= BASE_PATH ?>admin/riwayat.php">
                    <i class="fas fa-history"></i> Riwayat Diagnosa
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item mt-3">
                <a class="nav-link" href="<?= BASE_PATH ?>">
                    <i class="fas fa-external-link-alt"></i> Kembali ke Website
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <?php 
            // Content will be inserted here
            if (isset($page_title)) {
                echo '<h2 class="mb-4">' . $page_title . '</h2>';
            }
            ?>