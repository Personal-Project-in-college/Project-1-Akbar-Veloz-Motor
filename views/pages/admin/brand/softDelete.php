<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM brands WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $brandName = $stmt->fetchColumn();

    if (!$brandName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    $softDeleteBrand = $koneksi->prepare("UPDATE brands SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteBrand->execute([$id]);

    $softDeleteVehiclesModels = $koneksi->prepare("UPDATE vehicle_models SET deleted_by_brand_at = NOW() WHERE brand_id = ?");
    $softDeleteVehiclesModels->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Merek <strong>" . htmlspecialchars($brandName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus merek."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
