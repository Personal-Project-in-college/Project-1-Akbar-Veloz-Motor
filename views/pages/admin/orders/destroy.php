<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Ambil nama customer dan vehicle_id
    $getOrderQuery = $koneksi->prepare("
        SELECT c.name AS customer_name, o.vehicle_id 
        FROM orders o 
        JOIN customers c ON o.customer_id = c.id 
        WHERE o.id = ? AND c.deleted_at IS NULL
    ");
    $getOrderQuery->execute([$id]);
    $orderData = $getOrderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$orderData) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanen."]);
        exit;
    }

    $customerName = $orderData['customer_name'];
    $vehicleId = $orderData['vehicle_id'];

    // Hapus dari table test_drivers
    $deleteTestDriverQuery = $koneksi->prepare("DELETE FROM test_drivers WHERE order_id = ?");
    $deleteTestDriverQuery->execute([$id]);

    // Hapus dari table transactions
    $deleteTransactionQuery = $koneksi->prepare("DELETE FROM transactions WHERE order_id = ?");
    $deleteTransactionQuery->execute([$id]);

    // Hapus dari table orders
    $deleteOrderQuery = $koneksi->prepare("DELETE FROM orders WHERE id = ?");
    $isDeleted = $deleteOrderQuery->execute([$id]);

    // Update status kendaraan jadi available
    $updateVehicleQuery = $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?");
    $updateVehicleQuery->execute([$vehicleId]);

    if ($isDeleted) {
        echo json_encode([
            'success' => true,
            'message' => "Pesanan customer <strong>" . htmlspecialchars($customerName) . "</strong> berhasil dihapus permanen."
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => "Gagal menghapus permanen pesanan customer."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
