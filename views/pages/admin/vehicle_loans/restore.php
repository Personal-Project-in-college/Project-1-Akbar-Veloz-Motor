<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getVehicleLoanQuery = $koneksi->prepare("SELECT vehicle_id FROM vehicle_loans WHERE id = ? AND deleted_at IS NOT NULL");
    $getVehicleLoanQuery->execute([$id]);
    $vehicleId = $getVehicleLoanQuery->fetchColumn();

    if (!$vehicleId) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreVehiclesLoansQuery = $koneksi->prepare("UPDATE vehicle_loans SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restoreVehiclesLoansQuery->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Peminjaman kendaraan <strong>" . htmlspecialchars($vehicleId) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan peminjaman kendaraan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
