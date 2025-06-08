<?php

function deleteFileVehiclePhotos($koneksi, $vehicle_id)
{
    // Ambil semua data foto yang sudah soft-delete
    $getPhotosQuery = $koneksi->prepare("SELECT photo_path, deleted_at, deleted_by_vehicle_at FROM vehicle_photos WHERE vehicle_id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
    $getPhotosQuery->execute([$vehicle_id]);
    $photos = $getPhotosQuery->fetchAll(PDO::FETCH_ASSOC);

    foreach ($photos as $photo) {
        // Tentukan basePath berdasarkan kondisi
        if (!is_null($photo['deleted_at']) && is_null($photo['deleted_by_vehicle_at'])) {
            $basePath = '../../../../../storage/';
        } else {
            $basePath = '../../../../storage/';
        }

        $filePath = $basePath . $photo['photo_path'];

        if (file_exists($filePath)) {
            @unlink($filePath); // Hapus file fisik, abaikan warning
        }
    }

    // Hapus data dari DB
    $deleteRecordsQuery = $koneksi->prepare("DELETE FROM vehicle_photos WHERE vehicle_id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
    $deleteRecordsQuery->execute([$vehicle_id]);
}
