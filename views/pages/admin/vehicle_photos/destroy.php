<?php
include '../../../../config/koneksi.php';
include '../../../../helpers/functionDeleteFileVehiclePhoto.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $koneksi->prepare("SELECT vehicle_id FROM vehicle_photos WHERE id = ?");
    $query->execute([$id]);
    $data = $query->fetch();

    if ($data) {
        deleteFileVehiclePhotos($koneksi, $data['vehicle_id']);
    }
}

header('Location: delete.php');
exit;
