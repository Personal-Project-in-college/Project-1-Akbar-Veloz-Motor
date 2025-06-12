<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT id FROM vehicles WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $vehicleId = $stmt->fetchColumn();

    if (!$vehicleId) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreVehicleDocuments = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NULL WHERE vehicle_id = ?");
    $restoreVehicleDocuments->execute([$id]);

    $restoreVehiclePhotos = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NULL WHERE vehicle_id = ?");
    $restoreVehiclePhotos->execute([$id]);

    $restoreVehicles = $koneksi->prepare("UPDATE vehicles SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreVehicles->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Kendaraan <strong>" . htmlspecialchars($vehicleId) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan Kendaraan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
