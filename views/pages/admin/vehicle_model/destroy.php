<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM vehicle_models WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $vehicleModelName = $stmt->fetchColumn();

    if (!$vehicleModelName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    $deleteBrandQuery = $koneksi->prepare("DELETE FROM vehicle_models WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $deleteBrandQuery->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Kendaraan model <strong>" . htmlspecialchars($vehicleModelName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent Kendaraan model."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
