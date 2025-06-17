<?php
require_once('../../../../config/midtrans_config.php');
require_once('../../../../config/koneksi.php');

$json_result = file_get_contents('php://input');
$data = json_decode($json_result);

if (!$data || !isset($data->order_id)) {
    http_response_code(400);
    echo 'Invalid callback';
    exit;
}
$orderId = $data->order_id;
$transactionStatus = $data->transaction_status;
$paymentRef = $data->transaction_id ?? null;

// Coba ambil angka ID asli transaksi (setelah tanda '-')
$parts = explode('-', $orderId);
$transactionId = end($parts); // ambil bagian terakhir

// Logging
file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - Callback: $json_result - Parsed ID: $transactionId" . PHP_EOL, FILE_APPEND);

// Mapping status Midtrans ke status lokal
$statusMap = [
    'settlement' => 'paid',
    'capture' => 'paid',
    'pending' => 'pending',
    'deny' => 'failed',
    'cancel' => 'cancelled',
    'expire' => 'failed',
    'failure' => 'failed',
];

$status = $statusMap[$transactionStatus] ?? 'pending';

// Update status transaksi
$update = $koneksi->prepare("UPDATE transactions SET status = ?, payment_gateway_ref = ?, updated_at = NOW() WHERE id = ?");
$executed = $update->execute([$status, $paymentRef, $transactionId]);

if (!$executed) {
    file_put_contents('callback_log.txt', "DB Error: " . implode(', ', $update->errorInfo()) . PHP_EOL, FILE_APPEND);
}

echo 'OK';

