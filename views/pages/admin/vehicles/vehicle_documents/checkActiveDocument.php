<?php
include '../../../../../config/koneksi.php';

$vehicleId = $_GET['vehicle_id'] ?? null;

$stmt = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = ? AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL");
$stmt->execute([$vehicleId]);
$count = $stmt->fetchColumn();

echo json_encode(['active' => $count > 0]);
