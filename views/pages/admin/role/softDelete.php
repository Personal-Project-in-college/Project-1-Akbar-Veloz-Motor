<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Cek role masih aktif
    $stmt = $koneksi->prepare("SELECT name FROM roles WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $roleName = $stmt->fetchColumn();

    if (!$roleName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    // Cek apakah role ini sedang digunakan oleh user
    $checkUser = $koneksi->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
    $checkUser->execute([$id]);
    $usedCount = $checkUser->fetchColumn();

    if ($usedCount > 0) {
        echo json_encode([
            'success' => false,
            'message' => "Jabatan <strong>" . htmlspecialchars($roleName) . "</strong> tidak bisa dihapus karena sedang digunakan oleh $usedCount Karyawan."
        ]);
        exit;
    }

    // Soft delete role
    $softDeleteRole = $koneksi->prepare("UPDATE roles SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteRole->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Jabatan <strong>" . htmlspecialchars($roleName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus Jabatan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
