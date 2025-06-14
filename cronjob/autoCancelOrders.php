<?php
include '../../../../config/koneksi.php';

// Update semua order yang deleted_at lebih dari 5 menit dan status belum cancelled
$query = $koneksi->prepare("UPDATE orders SET status = 'cancelled' WHERE deleted_at IS NOT NULL AND status != 'cancelled' AND TIMESTAMPDIFF(MINUTE, deleted_at, NOW()) > 5");

$query->execute();

echo "Auto-cancel executed: " . $query->rowCount() . " rows updated.";
