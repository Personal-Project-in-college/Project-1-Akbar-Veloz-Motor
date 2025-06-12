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
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreRole = $koneksi->prepare("UPDATE roles SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreRole->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Jabatan <strong>" . htmlspecialchars($roleName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan jabatan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
