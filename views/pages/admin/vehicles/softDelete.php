<?php
include '../../../../config/koneksi.php';
// 🔗 Sambungkan ke database

// 🪢 Ambil ID cabang dari parameter URL
$id = $_GET['id'];

// ⬇️ Soft delete: Update kolom deleted_at, bukan hapus permanen
$data = $koneksi->prepare("UPDATE vehicles SET deleted_at = NOW() WHERE id = ?");
$data->execute([$id]);

// ⬇️ Soft delete dokumen kendaraan yang punya branch ini
$hapusDokumenKendaraan = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
$hapusDokumenKendaraan->execute([$id]);

// ⬇️ Soft delete photo kendaraan yang punya branch ini
$hapusPhotoKendaraan = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
$hapusPhotoKendaraan->execute([$id]);

// 🚀 Setelah update, alihkan kembali ke halaman index
header("Location: index.php");
exit;