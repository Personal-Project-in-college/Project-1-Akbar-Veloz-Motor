<?php
include '../../../../config/koneksi.php';

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header("Location: index.php");
    exit;
}

// Ambil ID kendaraan dulu sebelum hapus
$stmt = $koneksi->prepare("SELECT vehicle_id FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$vehicle_id = $stmt->fetchColumn();

// Update status kendaraan jadi available
$stmt = $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?");
$stmt->execute([$vehicle_id]);

// Hapus order
$stmt = $koneksi->prepare("DELETE FROM orders WHERE id = ?");
$stmt->execute([$order_id]);

header("Location: index.php");
exit;
?>
