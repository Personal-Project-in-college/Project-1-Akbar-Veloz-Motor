<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT id, status FROM vehicles WHERE id = ? AND deleted_at IS NULL AND deleted_by_branch_at IS NULL");
    $stmt->execute([$id]);
    $vehicleId = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehicleId) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    if (!in_array($vehicleId['status'], ['available', 'sold'])) {
        echo json_encode(['success' => false, 'message' => "Gagal dihapus jika status-nya <strong>'Tersedia'</strong> atau <strong>'Terjual'</strong>."]);
        exit;
    }

    $softDeleteVehicle = $koneksi->prepare("UPDATE vehicles SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteVehicle->execute([$id]);

    $softDeleteVehicleDocuments = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
    $softDeleteVehicleDocuments->execute([$id]);

    $softDeleteVehiclePhotos = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
    $softDeleteVehiclePhotos->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Kendaraan <strong>" . htmlspecialchars($vehicleId['id']) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus kendaraan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
