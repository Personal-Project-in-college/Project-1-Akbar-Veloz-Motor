<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Restore partner
    $data = $koneksi->prepare("UPDATE vehicles SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);

    // ⬇️ Restore dokumen kendaraan yang kendaraan-nya ini dan hanya kendaraan yang deleted_at nya null
    $restoreDokumenKendaraan = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NULL WHERE vehicle_id = ?");
    $restoreDokumenKendaraan->execute([$_GET['id']]);

    // ⬇️ Restore photo kendaraan yang kendaraan-nya ini dan hanya kendaraan yang deleted_at nya null
    $restorePhotoKendaraan = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NULL WHERE vehicle_id = ?");
    $restorePhotoKendaraan->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
