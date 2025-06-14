<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getNameBrandQuery = $koneksi->prepare("SELECT name FROM brands WHERE id = ? AND deleted_at IS NOT NULL");
    $getNameBrandQuery->execute([$id]);
    $brandName = $getNameBrandQuery->fetchColumn();

    if (!$brandName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }
    
    $destroyVehiclesModelsQuery = $koneksi->prepare("DELETE FROM vehicle_models WHERE brand_id = ?");
    $destroyVehiclesModelsQuery->execute([$id]);

    $destroyBrandQuery = $koneksi->prepare("DELETE FROM brands WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $destroyBrandQuery->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Merek <strong>" . htmlspecialchars($brandName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent merek."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
