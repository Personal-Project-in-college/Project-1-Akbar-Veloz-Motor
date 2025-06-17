<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getCustomerQuery = $koneksi->prepare("SELECT name FROM customers WHERE id = ? AND deleted_at IS NULL");
    $getCustomerQuery->execute([$id]);
    $customerName = $getCustomerQuery->fetchColumn();

    if (!$customerName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    $softDeleteCustomerQuery = $koneksi->prepare("UPDATE customers SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteCustomerQuery->execute([$id]);

    // Soft Delete ke order -> transaksi

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Customers <strong>" . htmlspecialchars($customerName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus customers."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
