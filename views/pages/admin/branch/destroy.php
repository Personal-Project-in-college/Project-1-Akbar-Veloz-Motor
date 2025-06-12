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
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    $getVehicleIdsQuery = $koneksi->prepare("SELECT id FROM vehicles WHERE branch_id = ?");
    $getVehicleIdsQuery->execute([$id]);
    $vehicleIds = $getVehicleIdsQuery->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($vehicleIds)) {
        foreach ($vehicleIds as $vehicleId) {
        
            deleteFileVehiclePhotos($koneksi, $vehicleId);

            $deletePhotosQuery = $koneksi->prepare("DELETE FROM vehicle_photos WHERE vehicle_id = ?");
            $deletePhotosQuery->execute([$vehicleId]);
        
            $getDocumentIdsQuery = $koneksi->prepare("SELECT id FROM vehicle_documents WHERE vehicle_id = ?");
            $getDocumentIdsQuery->execute([$vehicleId]);
            $documentIds = $getDocumentIdsQuery->fetchAll(PDO::FETCH_COLUMN);

            foreach ($documentIds as $documentId) {
                deleteFileVehicleDocument($koneksi, $documentId);
            }
        }

        $deleteVehiclesQuery = $koneksi->prepare("DELETE FROM vehicles WHERE branch_id = ?");
        $deleteVehiclesQuery->execute([$id]);
    }

    $deleteBranch = $koneksi->prepare("DELETE FROM branches WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $deleteBranch->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Merek <strong>" . htmlspecialchars($branchName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent merek."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
