<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getCustomerQuery = $koneksi->prepare("SELECT name FROM customers WHERE id = ? AND deleted_at IS NOT NULL");
    $getCustomerQuery->execute([$id]);
    $customerName = $getCustomerQuery->fetchColumn();

    if (!$customerName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    // restore order -> transaksi

    $restoreCustomerQuery = $koneksi->prepare("UPDATE customers SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreCustomerQuery->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Pelanggan <strong>" . htmlspecialchars($customerName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan Pelanggan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
