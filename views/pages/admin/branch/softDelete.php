<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getBranchQuery = $koneksi->prepare("SELECT name FROM branches WHERE id = ? AND deleted_at IS NULL");
    $getBranchQuery->execute([$id]);
    $branchName = $getBranchQuery->fetchColumn();

    if (!$branchName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    $softDeleteBranch = $koneksi->prepare("UPDATE branches SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteBranch->execute([$id]);

    $softDeleteVehicles = $koneksi->prepare("UPDATE vehicles SET deleted_by_branch_at = NOW() WHERE branch_id = ?");
    $softDeleteVehicles->execute([$id]);

    $softDeleteDocuments = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NOW() WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ?)");
    $softDeleteDocuments->execute([$id]);

    $softDeletePhotos = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NOW() WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ?)");
    $softDeletePhotos->execute([$id]);

    // $softDeleteServices = $koneksi->prepare("UPDATE vehicle_services SET deleted_by_vehicle_at = NOW() WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ?)");
    // $softDeleteServices->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Cabang <strong>" . htmlspecialchars($branchName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus cabang."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
