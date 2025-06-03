<?php
function deleteFileVehiclePhotos($koneksi, $vehicle_id) {
    // Ambil semua path file yang masih tersimpan di storage
    $stmt = $koneksi->prepare("SELECT photo_path FROM vehicle_photos WHERE vehicle_id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
    $stmt->execute([$vehicle_id]);
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($photos as $photo) {
        $filePath = '../../../../storage/' . $photo['photo_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Hapus record dari tabel
    $stmt = $koneksi->prepare("DELETE FROM vehicle_photos WHERE vehicle_id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
    $stmt->execute([$vehicle_id]);
}
