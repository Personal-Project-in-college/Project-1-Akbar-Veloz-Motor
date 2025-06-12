<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM vehicle_models WHERE id = ? AND deleted_at IS NULL AND deleted_by_brand_at IS NULL");
    $stmt->execute([$id]);
    $vehicleModelName = $stmt->fetchColumn();

    if (!$vehicleModelName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    $softDeleteVehiclesModels = $koneksi->prepare("UPDATE vehicle_models SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteVehiclesModels->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Model kendaraan <strong>" . htmlspecialchars($vehicleModelName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus model kendaraan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
