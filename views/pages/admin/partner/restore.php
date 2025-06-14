<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getPartnerQuery = $koneksi->prepare("SELECT name FROM partners WHERE id = ? AND deleted_at IS NOT NULL");
    $getPartnerQuery->execute([$id]);
    $partnerName = $getPartnerQuery->fetchColumn();

    if (!$partnerName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dikembalikan."]);
        exit;
    }

    $restoreVehiclesLoansQuery = $koneksi->prepare("UPDATE vehicle_loans SET deleted_by_partner_at = NULL WHERE partner_id = ?");
    $restoreVehiclesLoansQuery->execute([$id]);

    $restorePartnerQuery = $koneksi->prepare("UPDATE partners SET deleted_at = NULL WHERE id = ?");
    $isRestore = $restorePartnerQuery->execute([$id]);

    if ($isRestore) {
        echo json_encode(['success' => true, 'message' => "Partner <strong>" . htmlspecialchars($partnerName) . "</strong> berhasil dikembalikan."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat mengembalikan partner."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
