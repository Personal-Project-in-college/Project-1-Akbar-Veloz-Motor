<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionDeleteFileVehiclePhoto.php'; 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT id FROM vehicles WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $vehicleId = $stmt->fetchColumn();

    if (!$vehicleId) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    if ($vehicleId) {

        $destroyVehicleDocuments = $koneksi->prepare("DELETE FROM vehicle_documents WHERE vehicle_id = ?");
        $destroyVehicleDocuments->execute([$id]);

        $softDeleteVehiclePhotos = $koneksi->prepare("UPDATE vehicle_photos SET deleted_at = NOW() WHERE vehicle_id = ? AND deleted_at IS NULL");
        $softDeleteVehiclePhotos->execute([$id]);

        deleteFileVehiclePhotos($koneksi, $id);

        $destroyBrand = $koneksi->prepare("DELETE FROM vehicles WHERE id = ? AND (deleted_at IS NOT NULL OR deleted_by_branch_at IS NOT NULL)");
        $isDestroy = $destroyBrand->execute([$id]);

        $vehicleFolder = '../../../../storage/vehicles/vehicle_' . $id;

        if (is_dir($vehicleFolder)) {
            function deleteFolder($folderPath)
            {
                foreach (scandir($folderPath) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    $path = $folderPath . DIRECTORY_SEPARATOR . $item;
                    is_dir($path) ? deleteFolder($path) : unlink($path);
                }
                rmdir($folderPath);
            }
            deleteFolder($vehicleFolder);
        }

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Merek <strong>" . htmlspecialchars($vehicleId) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent merek."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}

}