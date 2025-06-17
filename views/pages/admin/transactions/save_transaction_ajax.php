<?php
include '../../../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $payment_type = $_POST['payment_type'];
    $down_payment = $_POST['down_payment'];
    $payment_method = $_POST['payment_method'];
    $remaining_amount = $_POST['remaining_amount'];

    // Validasi data
    if (!$order_id || !$payment_type) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Simpan ke database (update atau insert tergantung logika kamu)
    $stmt = $koneksi->prepare("UPDATE transactions SET payment_type=?, down_payment=?, payment_method=?, remaining_amount=? WHERE order_id=?");
    $success = $stmt->execute([$payment_type, $down_payment, $payment_method, $remaining_amount, $order_id]);

    if ($success) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal simpan data transaksi']);
    }
}
