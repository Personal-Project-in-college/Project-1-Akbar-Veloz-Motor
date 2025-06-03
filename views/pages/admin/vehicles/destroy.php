<?php
include '../../../../config/koneksi.php';
include '../../../../helpers/functionDeleteFileVehiclePhoto.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 🔥 Hapus dokumen kendaraan
    $hapusDokumenKendaraan = $koneksi->prepare("DELETE FROM vehicle_documents WHERE vehicle_id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
    $hapusDokumenKendaraan->execute([$id]);

    // 🔥 Hapus foto kendaraan (panggil helper!)
    deleteFileVehiclePhotos($koneksi, $id);

    // 🔥 Hapus kendaraan
    $data = $koneksi->prepare("DELETE FROM vehicles WHERE id = ? AND (deleted_at IS NOT NULL OR deleted_by_branch_at IS NOT NULL)");
    $data->execute([$id]);
}

header('Location: delete.php');
exit;
