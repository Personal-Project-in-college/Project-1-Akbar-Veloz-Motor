<?php
include '../../../../config/koneksi.php';
// 🔗 Sambungkan ke database

// 🪢 Ambil ID users dari parameter URL
$id = $_GET['id'];

// ⬇️ Soft delete: Update kolom deleted_at, bukan hapus permanen
$data = $koneksi->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
$data->execute([$id]);

// 🚀 Setelah update, alihkan kembali ke halaman index
header("Location: index.php");
exit;