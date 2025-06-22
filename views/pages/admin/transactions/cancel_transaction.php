<?php
include '../../../../config/koneksi.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> ID transaksi tidak ditemukan di URL.";
    header("Location: transaction.php");
    exit;
}

try {
    // Ambil order_id & vehicle_id dari transaksi
    $stmt = $koneksi->prepare("SELECT t.order_id, o.vehicle_id 
        FROM transactions t
        JOIN orders o ON t.order_id = o.id
        WHERE t.id = ? AND t.deleted_at IS NULL");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        $_SESSION['danger_message'] = "<strong>Error: </strong> Data transaksi tidak ditemukan atau sudah dihapus.";
        header("Location: transaction.php");
        exit;
    }

    $order_id = $data['order_id'];
    $vehicle_id = $data['vehicle_id'];

    // Mulai transaksi
    $koneksi->beginTransaction();

    // Update status orders → cancelled
    $koneksi->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?")
        ->execute([$order_id]);

    // Update status transaksi → cancelled
    $koneksi->prepare("UPDATE transactions SET status = 'cancelled' WHERE id = ?")
        ->execute([$id]);

    // Update kendaraan jadi available
    $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?")
        ->execute([$vehicle_id]);

    $koneksi->commit();

    $_SESSION['success_message'] = "Transaksi berhasil dibatalkan.";
    header("Location: transaction.php");
} catch (PDOException $e) {
    $koneksi->rollBack();
    $_SESSION['danger_message'] = "Gagal membatalkan transaksi: " . $e->getMessage();
    header("Location: transaction.php");
}
