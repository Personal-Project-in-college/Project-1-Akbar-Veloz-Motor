<?php
include '../../../../config/koneksi.php';
include '../../../../config/midtrans_config.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['error' => 'ID order tidak ditemukan']);
    exit;
}

// Ambil data transaksi + customer berdasarkan order_id
$query = $koneksi->prepare("
    SELECT 
        t.id AS transaction_id,
        t.grand_total,
        t.payment_type,
        o.id AS order_id,
        c.name AS customer_name,
        c.email AS customer_email,
        c.phone AS customer_phone
    FROM transactions t
    JOIN orders o ON t.order_id = o.id
    JOIN customers c ON o.customer_id = c.id
    WHERE t.order_id = ?
");
$query->execute([$id]);
$data = $query->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo json_encode(['error' => 'Data transaksi tidak ditemukan']);
    exit;
}

// Siapkan payload Snap
$params = [
    'transaction_details' => [
        'order_id' => 'TRX-' . $data['transaction_id'],
        'gross_amount' => (int) $data['grand_total']
    ],
    'customer_details' => [
        'first_name' => $data['customer_name'],
        'email' => $data['customer_email'],
        'phone' => $data['customer_phone']
    ],
    'callbacks' => [
        'finish' => 'http://localhost/projectmu/transactions/finish.php'
    ],
    'expiry' => [
        'start_time' => date("Y-m-d H:i:s O"),
        'unit' => 'minutes',
        'duration' => 60
    ]
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    echo json_encode(['snap_token' => $snapToken]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
