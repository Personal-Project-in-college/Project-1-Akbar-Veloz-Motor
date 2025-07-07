<?php
session_start();
include '../../../../../config/koneksi.php';
include '../../../../../helpers/functionDeleteFileVehicleDocument.php';

include '../../../../../helpers/functionCheckLogin.php';
checkLogin();

$vehicle_id = $_GET['vehicle_id'] ?? null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Langsung panggil helper dan passing koneksi + id dokumen
    $deleted = deleteFileVehicleDocument($koneksi, $id);

    if ($vehicle_id) {
        // Pesan jika ID berhasil diambil, lebih spesifik.
        $_SESSION['danger_message'] = "Dokumen Kendaraan <strong>" . htmlspecialchars($vehicle_id) . "</strong> berhasil dihapus selamanya.";
    } else {
        // Pesan fallback jika karena suatu hal ID tidak terambil.
        $_SESSION['danger_message'] = "Kendaraan tidak ditemukan atau sudah dihapus sebelumnya.";
    }

    
    // Redirect setelah hapus
    header('Location: ../delete-vehicle-documents.php?id=' . urlencode($vehicle_id));
    exit;
}
