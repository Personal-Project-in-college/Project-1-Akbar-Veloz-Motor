<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $koneksi->prepare("SELECT name FROM partners WHERE id = ? AND deleted_at IS NOT NULL");
    $stmt->execute([$id]);
    $partnerName = $stmt->fetchColumn();

    if (!$partnerName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreVehiclesLoans = $koneksi->prepare("UPDATE vehicle_loans SET deleted_by_partner_at = NULL WHERE partner_id = ?");
    $restoreVehiclesLoans->execute([$id]);

    $restorePartner = $koneksi->prepare("UPDATE partners SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restorePartner->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Partner <strong>" . htmlspecialchars($partnerName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan partner."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
