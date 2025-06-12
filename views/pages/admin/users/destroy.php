<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM users WHERE id = ? AND (deleted_at IS NOT NULL OR deleted_by_role_at IS NOT NULL)");
    $stmt->execute([$id]);
    $userName = $stmt->fetchColumn();

    if (!$userName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    $destroyUser = $koneksi->prepare("DELETE FROM users WHERE id = ? AND (deleted_at IS NOT NULL OR deleted_by_role_at IS NOT NULL)");
    $isDestroy = $destroyUser->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Karyawan <strong>" . htmlspecialchars($userName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent karyawan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
