<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $orderId = $_POST['id'];

    // Ambil data order beserta kendaraan dan customer
    $getOrderQuery = $koneksi->prepare("SELECT o.id, o.vehicle_id, o.status, c.name AS customer_name FROM orders o JOIN customers c ON o.customer_id = c.id WHERE o.id = ? AND o.deleted_at IS NOT NULL");
    $getOrderQuery->execute([$orderId]);
    $orderData = $getOrderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$orderData) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah aktif."]);
        exit;
    }

    $vehicleId = $orderData['vehicle_id'];
    $orderStatus = $orderData['status'];
    $customerName = $orderData['customer_name'];

    // Cek status kendaraan
    $checkVehicleQuery = $koneksi->prepare("SELECT status FROM vehicles WHERE id = ?");
    $checkVehicleQuery->execute([$vehicleId]);
    $vehicleStatus = $checkVehicleQuery->fetchColumn();

    if ($vehicleStatus !== 'available') {
        echo json_encode(['success' => false, 'message' => "Kendaraan tidak tersedia (status: $vehicleStatus). Restore dibatalkan."]);
        exit;
    }

    // Restore order
    $restoreOrderQuery = $koneksi->prepare("UPDATE orders SET deleted_at = NULL WHERE id = ?");
    $isRestored = $restoreOrderQuery->execute([$orderId]);

    // Update status sesuai tipe
    if ($isRestored) {
        if ($orderStatus === 'test_driver') {
            $updateTD = $koneksi->prepare("UPDATE test_drivers SET status = 'test_driver' WHERE order_id = ?");
            $updateTD->execute([$orderId]);
        } elseif ($orderStatus === 'transaction') {
            $updateTrans = $koneksi->prepare("UPDATE transactions SET status = 'pending' WHERE order_id = ?");
            $updateTrans->execute([$orderId]);
        }

        echo json_encode(['success' => true, 'message' => "Order milik <strong>" . htmlspecialchars($customerName) . "</strong> berhasil dikembalikan beserta status terkait."
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan data order."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
