<?php
include '../../../../config/koneksi.php';
// 🔗 Sambungkan ke database

// 🪢 Ambil ID cabang dari parameter URL
$id = $_GET['id'];

// ⬇️ Soft delete Cabang: Update kolom deleted_at, bukan hapus permanen
$data = $koneksi->prepare("UPDATE branches SET deleted_at = NOW() WHERE id = ?");
$data->execute([$id]);

// ⬇️ Soft delete kendaraan yang punya branch ini
$hapusKendaraan = $koneksi->prepare("UPDATE vehicles SET deleted_by_branch_at = NOW() WHERE branch_id = ?");
$hapusKendaraan->execute([$id]);

// ⬇️ // Soft delete dokumen kendaraan berdasarkan vehicle di branch ini
$getVehicleId = $koneksi->prepare("SELECT id FROM vehicles WHERE branch_id = ?");
$getVehicleId->execute([$id]);
$vehicles = $getVehicleId->fetchAll(PDO::FETCH_COLUMN);

foreach ($vehicles as $vehicleId) {
    $hapusDokumenKendaraan = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
    $hapusDokumenKendaraan->execute([$vehicleId]);
}

// ⬇️ // Soft delete Photo kendaraan berdasarkan vehicle di branch ini
foreach ($vehicles as $vehicleId) {
    $hapusPhotoKendaraan = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
    $hapusPhotoKendaraan->execute([$vehicleId]);
}


// 🚀 Setelah update, alihkan kembali ke halaman index
header("Location: index.php");
exit;