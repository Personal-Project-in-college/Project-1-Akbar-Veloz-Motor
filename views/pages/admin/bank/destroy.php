<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Cek apakah data bank ada dan sudah soft deleted
    $getBankQuery = $koneksi->prepare("SELECT bank_name FROM banks WHERE id = ? AND deleted_at IS NOT NULL");
    $getBankQuery->execute([$id]);
    $bankName = $getBankQuery->fetchColumn();

    if (!$bankName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanen."]);
        exit;
    }

    // Hapus permanen
    $destroyBankQuery = $koneksi->prepare("DELETE FROM banks WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $destroyBankQuery->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Bank <strong>" . htmlspecialchars($bankName) . "</strong> berhasil dihapus permanen."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus permanen bank."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
