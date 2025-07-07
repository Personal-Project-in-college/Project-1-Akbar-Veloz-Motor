<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Ambil nama bank
    $getBankQuery = $koneksi->prepare("SELECT bank_name FROM banks WHERE id = ? AND deleted_at IS NULL");
    $getBankQuery->execute([$id]);
    $bankName = $getBankQuery->fetchColumn();

    if (!$bankName) {
        echo json_encode(['success' => false, 'message' => "Data bank tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    // Soft delete bank
    $softDeleteBankQuery = $koneksi->prepare("UPDATE banks SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteBankQuery->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Bank <strong>" . htmlspecialchars($bankName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus bank."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
