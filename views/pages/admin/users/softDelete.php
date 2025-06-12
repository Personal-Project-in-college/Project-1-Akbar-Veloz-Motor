<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM users WHERE id = ? AND deleted_at IS NULL AND deleted_by_role_at IS NULL");
    $stmt->execute([$id]);
    $userName = $stmt->fetchColumn();

    if (!$userName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    $softDeleteUser = $koneksi->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteUser->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Karyawan <strong>" . htmlspecialchars($userName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus karyawan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
