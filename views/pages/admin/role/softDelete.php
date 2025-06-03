<?php
include '../../../../config/koneksi.php';
// 🔗 Sambungkan ke database

// 🪢 Ambil ID Role dari parameter URL
$id = $_GET['id'];

// ⬇️ Soft delete Role: Update kolom deleted_at, bukan hapus permanen
$data = $koneksi->prepare("UPDATE roles SET deleted_at = NOW() WHERE id = ?");
$data->execute([$id]);

// ⬇️ Soft delete kendaraan yang punya branch ini
$hapusUser = $koneksi->prepare("UPDATE users SET deleted_by_role_at = NOW() WHERE role_id = ?");
$hapusUser->execute([$id]);

// 🚀 Setelah update, alihkan kembali ke halaman index
header("Location: index.php");
exit;