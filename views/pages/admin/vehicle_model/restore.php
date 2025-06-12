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
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreVehiclesModels = $koneksi->prepare("UPDATE vehicle_models SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreVehiclesModels->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Model kendaraan <strong>" . htmlspecialchars($vehicleModelName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan model kendaraan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
