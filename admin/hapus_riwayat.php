<?php
session_start();
require_once(__DIR__ . '/../includes/functions.php');
define('BASE_PATH', '/SISPAK_B/');

// Ambil ID dari parameter GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'ID riwayat tidak valid!'
    ];
    header('Location: ' . BASE_PATH . 'admin/riwayat.php');
    exit();
}

// Query untuk menghapus data
$query = "DELETE FROM riwayat WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    $_SESSION['flash_message'] = [
        'type' => 'success',
        'message' => 'Riwayat diagnosis berhasil dihapus!'
    ];
} else {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Gagal menghapus riwayat diagnosis: ' . mysqli_error($conn)
    ];
}

header('Location: ' . BASE_PATH . 'admin/riwayat.php');
exit();
?>