<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT vehicle_id FROM vehicle_loans WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $vehicleId = $stmt->fetchColumn();

    if (!$vehicleId) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }
    
    $checkVehicle_id = $vehicleId['vehicle_id'] ?? null;
    $checkLoanStatus = $vehicleId['status'] ?? null;

    if ($checkVehicle_id && $checkLoanStatus !== 'returned') {
        // Cek apakah kendaraan belum pernah terjual
        $checkStatusVehicle = $koneksi->prepare("SELECT COUNT(*) FROM orders WHERE vehicle_id = ? AND status IN ('transaction', 'finished')");
        $checkStatusVehicle->execute([$vehicle_id]);
        $countStatusVehicles = $checkStatusVehicle->fetchColumn();

        // Kalau tidak ditemukan di order, ubah status jadi available
        if ($countStatusVehicles == 0) {
            $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?");
            $updateStatusVehicle->execute([$checkVehicle_id]);
        }
    }

    $destroyVehicleLoan = $koneksi->prepare("DELETE FROM vehicle_loans WHERE id = ? AND deleted_at IS NOT NULL");
    $isDestroy = $destroyVehicleLoan->execute([$id]);

    if ($isDestroy) {
        echo json_encode(['success' => true, 'message' => "Peminjaman kendaraan <strong>" . htmlspecialchars($vehicleId) . "</strong> berhasil dihapus permanent."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent Peminjaman kendaraan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
