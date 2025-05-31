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
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/layout.css">
    <link rel="icon" type="image/png" href="<?= BASE_PATH ?>assets/img/favicon.png">
    
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler me-2" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <a class="navbar-brand" href="<?= BASE_PATH ?>admin/dashboard.php">
                    <i class="fas fa-cogs"></i> Admin Panel
                </a>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'pakar') : ?>
                <a class="navbar-brand" href="<?= BASE_PATH ?>admin/dashboard.php">
                    <i class="fas fa-user-md"></i> Pakar Panel
                </a>
            <?php endif; ?>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (isset($_SESSION['profile_pic']) && !empty($_SESSION['profile_pic'])) : ?>
                                <img src="<?= BASE_PATH . 'uploads/' . htmlspecialchars($_SESSION['profile_pic']) ?>" class="nav-user-img" alt="Profile">
                            <?php else : ?>
                                <i class="fas fa-user-circle me-1" style="font-size: 1.5rem;"></i>
                            <?php endif; ?>
                            <span class="ms-2"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></span>
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
        <div class="sidebar-header">
            <h4><i class="fas fa-stethoscope me-2"></i>Sistem Pakar Gigi</h4>
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
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_PATH ?>">
                    <i class="fas fa-external-link-alt"></i> Kembali ke Website
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            
            <!-- Page Header -->
            <div>
                <?php if (isset($page_title)) : ?>
                    <h2 class="mb-0 fw-bold">
                        <i class="fas fa-angle-right me-2"></i><?= $page_title ?>
                    </h2>
                <?php endif; ?>
            </div>
            <?php if (basename($_SERVER['PHP_SELF']) !== 'dashboard.php') : ?>
                <hr>
            <?php endif; ?>