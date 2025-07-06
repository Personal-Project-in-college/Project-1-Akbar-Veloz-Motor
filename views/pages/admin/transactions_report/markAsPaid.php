<?php
session_start();
include '../../../../config/koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    $transactionId = $_POST['transaction_id'];
    try {
        $update = $koneksi->prepare("UPDATE transactions SET status = 'paid', updated_at = NOW() WHERE id = ?");
        $update->execute([$transactionId]);

        echo json_encode(['success' => true, 'message' => 'Transaksi berhasil ditandai sebagai lunas.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui transaksi.']);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
exit;
