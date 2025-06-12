<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getRoleQuery = $koneksi->prepare("SELECT name FROM roles WHERE id = ? AND deleted_at IS NOT NULL");
    $getRoleQuery->execute([$id]);
    $roleName = $getRoleQuery->fetchColumn();

    if (!$roleName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    $destroyRoleQuery = $koneksi->prepare("DELETE FROM roles WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $destroyRoleQuery->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Jabatan <strong>" . htmlspecialchars($roleName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent jabatan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
