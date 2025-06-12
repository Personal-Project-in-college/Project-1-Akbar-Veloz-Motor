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
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }
    

    $destroyVehiclesModels = $koneksi->prepare("DELETE FROM vehicle_models WHERE brand_id = ?");
    $destroyVehiclesModels->execute([$id]);

    $destroyBrand = $koneksi->prepare("DELETE FROM brands WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $destroyBrand->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Merek <strong>" . htmlspecialchars($brandName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent merek."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
