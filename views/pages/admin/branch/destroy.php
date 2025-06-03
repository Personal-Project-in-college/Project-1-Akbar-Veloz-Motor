<?php
include '../../../../config/koneksi.php';
include '../../../../helpers/functionDeleteFileVehiclePhoto.php';

if (isset($_GET['id'])) {
    $branch_id = $_GET['id'];

    // ⬇️ Ambil semua ID kendaraan dalam branch
    $getVehicleId = $koneksi->prepare("SELECT id FROM vehicles WHERE branch_id = ?");
    $getVehicleId->execute([$branch_id]);
    $vehicles = $getVehicleId->fetchAll(PDO::FETCH_COLUMN);

    foreach ($vehicles as $vehicleId) {
        // ✅ Perbaiki soft-delete dokumen
        $hapusDokumenKendaraan = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
        $hapusDokumenKendaraan->execute([$vehicleId]);

        // ✅ Perbaiki soft-delete foto
        $hapusPhotoKendaraan = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
        $hapusPhotoKendaraan->execute([$vehicleId]);
    }

    // ✅ Hapus file fisik & record berdasarkan kondisi soft-delete
    foreach ($vehicles as $vehicleId) {
        deleteFileVehiclePhotos($koneksi, $vehicleId);
    }

    // ⬇️ Hapus kendaraan jika sudah soft-deleted oleh branch
    $deleteVehicles = $koneksi->prepare("DELETE FROM vehicles WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL");
    $deleteVehicles->execute([$branch_id]);

    // ⬇️ Hapus branch permanen
    $data = $koneksi->prepare("DELETE FROM branches WHERE id = ? AND deleted_at IS NOT NULL");
    $data->execute([$branch_id]);
}

header('Location: delete.php');
exit;
