<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM roles WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $roleName = $stmt->fetchColumn();

    if (!$roleName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    $destroyRole = $koneksi->prepare("DELETE FROM roles WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $destroyRole->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Jabatan <strong>" . htmlspecialchars($roleName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent jabatan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
