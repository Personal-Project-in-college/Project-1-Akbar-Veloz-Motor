<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getBrandQuery = $koneksi->prepare("SELECT name FROM brands WHERE id = ? AND deleted_at IS NOT NULL");
    $getBrandQuery->execute([$id]);
    $brandName = $getBrandQuery->fetchColumn();

    if (!$brandName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreVehiclesModelsQuery = $koneksi->prepare("UPDATE vehicle_models SET deleted_by_brand_at = NULL WHERE brand_id = ?");
    $restoreVehiclesModelsQuery->execute([$id]);

    $restoreBrandQuery = $koneksi->prepare("UPDATE brands SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreBrandQuery->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Merek <strong>" . htmlspecialchars($brandName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan merek."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
