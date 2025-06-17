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
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    $destroyCustomerQuery = $koneksi->prepare("DELETE FROM customers WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $destroyCustomerQuery->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Pelanggan <strong>" . htmlspecialchars($customerName) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent Pelanggan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
