<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ⬇️ Restore dokumen kendaraan terlebih dahulu
    $restoreDocuments = $koneksi->prepare("
        UPDATE vehicle_documents 
        SET deleted_by_vehicle_at = NULL 
        WHERE vehicle_id IN (
            SELECT id FROM vehicles 
            WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL
        ) AND deleted_by_vehicle_at IS NOT NULL
    ");
    $restoreDocuments->execute([$id]);

    // ⬇️ Restore photo kendaraan terlebih dahulu
    $restorePhoto = $koneksi->prepare("
        UPDATE vehicle_photos 
        SET deleted_by_vehicle_at = NULL 
        WHERE vehicle_id IN (
            SELECT id FROM vehicles 
            WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL
        ) AND deleted_by_vehicle_at IS NOT NULL
    ");
    $restorePhoto->execute([$id]);

    // ⬇️ Restore kendaraan
    $restoreVehicles = $koneksi->prepare("UPDATE vehicles SET deleted_by_branch_at = NULL WHERE branch_id = ?");
    $restoreVehicles->execute([$id]);

    // ⬇️ Restore branch
    $data = $koneksi->prepare("UPDATE branches SET deleted_at = NULL WHERE id = ?");
    $data->execute([$id]);
}

header('Location: delete.php');
exit;
