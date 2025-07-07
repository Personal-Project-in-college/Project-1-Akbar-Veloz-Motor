<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Ambil nama bank yang sudah dihapus
    $getBankQuery = $koneksi->prepare("SELECT bank_name FROM banks WHERE id = ? AND deleted_at IS NOT NULL");
    $getBankQuery->execute([$id]);
    $bankName = $getBankQuery->fetchColumn();

    if (!$bankName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    // Restore bank
    $restoreBankQuery = $koneksi->prepare("UPDATE banks SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreBankQuery->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Bank <strong>" . htmlspecialchars($bankName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan bank."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
