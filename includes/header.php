<?php
require_once(__DIR__ . '/../config/database.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Diagnosa Penyakit Gigi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/sispak/index.php">Sistem Pakar Penyakit Gigi</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/sispak/admin/dashboard.php">Admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/sispak/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sispak/konsultasi.php">Konsultasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sispak/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/sispak/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sispak/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sispak/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>

            </div>
        </div>
    </nav>
    
    <div class="container">