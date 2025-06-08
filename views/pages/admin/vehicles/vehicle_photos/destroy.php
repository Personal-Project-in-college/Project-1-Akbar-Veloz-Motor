<?php
include '../../../../../config/koneksi.php';
include '../../../../../helpers/functionDeleteFileVehiclePhoto.php';

$vehicle_id = $_GET['vehicle_id'] ?? null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $koneksi->prepare("SELECT vehicle_id FROM vehicle_photos WHERE id = ?");
    $query->execute([$id]);
    $data = $query->fetch();

    if ($data) {
        deleteFileVehiclePhotos($koneksi, $data['vehicle_id']);
    }
}

header('Location: ../delete-vehicle-documents.php?id=' . urlencode($vehicle_id));
exit;
