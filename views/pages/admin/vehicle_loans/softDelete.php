<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Ambil status dan vehicle_id dari peminjaman
    $getVehicleLoanQuery = $koneksi->prepare("SELECT status, vehicle_id FROM vehicle_loans WHERE id = ? AND deleted_at IS NULL");
    $getVehicleLoanQuery->execute([$id]);
    $loanData = $getVehicleLoanQuery->fetch(PDO::FETCH_ASSOC);

    if (!$loanData) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    if ($loanData['status'] === 'borrowed') {
        echo json_encode(['success' => false, 'message' => "Tidak bisa menghapus data peminjaman karena kendaraan belum dikembalikan."]);
        exit;
    }

    // Lanjutkan soft delete jika status sudah returned
    $softDeleteVehicleLoanQuery = $koneksi->prepare("UPDATE vehicle_loans SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeleteVehicleLoanQuery->execute([$id]);

    if ($isDeleted) {
        echo json_encode([
            'success' => true,
            'message' => "Peminjaman kendaraan <strong>" . htmlspecialchars($loanData['vehicle_id']) . "</strong> berhasil dihapus sementara."
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus peminjaman kendaraan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
