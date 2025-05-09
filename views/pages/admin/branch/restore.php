<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Restore branch
    $data = $koneksi->prepare("UPDATE branches SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);

    // ⬇️ Restore kendaraan yang branch-nya ini
    $restoreVehicles = $koneksi->prepare("UPDATE vehicles SET deleted_at = NULL WHERE branch_id = ?");
    $restoreVehicles->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
