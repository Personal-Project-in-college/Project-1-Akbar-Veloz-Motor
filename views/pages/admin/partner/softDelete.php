<?php
include '../../../../config/koneksi.php';
// 🔗 Sambungkan ke database

// 🪢 Ambil ID cabang dari parameter URL
$id = $_GET['id'];

// ⬇️ Soft delete: Update kolom deleted_at, bukan hapus permanen
$data = $koneksi->prepare("UPDATE partners SET deleted_at = NOW() WHERE id = ?");
$data->execute([$id]);

// ⬇️ Soft delete peminjaman kendaraan yang punya partner ini
$hapusPinjamKendaraan = $koneksi->prepare("UPDATE vehicle_loans SET deleted_by_partner_at = NOW() WHERE partner_id = ?");
$hapusPinjamKendaraan->execute([$id]);

// 🚀 Setelah update, alihkan kembali ke halaman index
header("Location: index.php");
exit;