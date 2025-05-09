<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Hapus kendaraan yang branch-nya ini (jika kendaraan juga sudah di-soft delete sebelumnya)
    $deleteVehicles = $koneksi->prepare("DELETE FROM vehicles WHERE branch_id = ? AND deleted_at IS NOT NULL");
    $deleteVehicles->execute([$_GET['id']]);

    // ⬇️ Hapus branch secara permanen jika memang sudah dihapus sebelumnya
    $data = $koneksi->prepare("DELETE FROM branches WHERE id = ? AND deleted_at IS NOT NULL");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
