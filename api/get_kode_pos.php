<?php
header('Content-Type: application/json');
require_once '../config/koneksi.php'; // Sesuaikan path

$id_kecamatan = $_GET['id_kecamatan'] ?? null;

if (!$id_kecamatan) {
    echo json_encode(['success' => false, 'message' => 'ID Kecamatan tidak ditemukan.']);
    exit();
}

try {
    $stmt = $koneksi->prepare("SELECT id, nama_kelurahan_desa, kode_pos FROM kelurahan_desa WHERE id_kecamatan = :id_kecamatan ORDER BY nama_kelurahan_desa ASC");
    $stmt->bindParam(':id_kecamatan', $id_kecamatan, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error mengambil data kode pos: ' . $e->getMessage()]);
}
?>