<?php
function deleteFileVehicleDocument($koneksi, $id)
{
    // Ambil data dokumen sesuai id yg dihapus (deleted_at IS NOT NULL)
    $stmt = $koneksi->prepare("SELECT * FROM vehicle_documents WHERE id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
    $stmt->execute([$id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) return false;

    // Penentuan basePath berdasarkan kondisi field
    if (!is_null($document['deleted_at']) && is_null($document['deleted_by_vehicle_at'])) {
        $basePath = '../../../../../storage/'; // Jika hanya deleted_at
    } else {
        $basePath = '../../../../storage/'; // Jika deleted_by_vehicle_at atau keduanya
    }


    // List file fields yg akan dihapus
    $fileFields = ['stnk', 'bpkb', 'service_note', 'nota', 'asuransi'];

    foreach ($fileFields as $field) {
        if (!empty($document[$field])) {
            $fullPath = $basePath . $document[$field];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    // Hapus data dari DB
    $delete = $koneksi->prepare("DELETE FROM vehicle_documents WHERE id = ?");
    $delete->execute([$id]);

    return true;
}
