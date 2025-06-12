<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM branches WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $branchName = $stmt->fetchColumn();

    if (!$branchName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreDocuments = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NULL WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL) AND deleted_by_vehicle_at IS NOT NULL");
    $restoreDocuments->execute([$id]);

    $restorePhotos = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NULL WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL) AND deleted_by_vehicle_at IS NOT NULL");
    $restorePhotos->execute([$id]);

    // $restoreServices = $koneksi->prepare("UPDATE vehicle_services SET deleted_by_vehicle_at = NULL WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL) AND deleted_by_vehicle_at IS NOT NULL");
    // $restoreServices->execute([$id]);

    $restoreVehicles = $koneksi->prepare("UPDATE vehicles SET deleted_by_branch_at = NULL WHERE branch_id = ?");
    $restoreVehicles->execute([$id]);

    $restoreBranch = $koneksi->prepare("UPDATE branches SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreBranch->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Cabang <strong>" . htmlspecialchars($branchName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan cabang."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
