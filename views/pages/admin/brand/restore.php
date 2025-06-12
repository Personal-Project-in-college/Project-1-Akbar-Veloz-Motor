<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM brands WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $brandName = $stmt->fetchColumn();

    if (!$brandName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreVehiclesModels = $koneksi->prepare("UPDATE vehicle_models SET deleted_by_brand_at = NULL WHERE brand_id = ?");
    $restoreVehiclesModels->execute([$id]);

    $restoreBrand = $koneksi->prepare("UPDATE brands SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreBrand->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Merek <strong>" . htmlspecialchars($brandName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan merek."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
