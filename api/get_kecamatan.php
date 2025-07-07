<?php
header('Content-Type: application/json');
require_once '../config/koneksi.php'; // Sesuaikan path

try {
    $stmt = $koneksi->prepare("SELECT id, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC");
    $stmt->execute();
    $kecamatan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $kecamatan]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error mengambil data kecamatan: ' . $e->getMessage()]);
}
?>