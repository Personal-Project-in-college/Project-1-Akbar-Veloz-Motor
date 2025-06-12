<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getPartnerQuery = $koneksi->prepare("SELECT name FROM partners WHERE id = ? AND deleted_at IS NULL");
    $getPartnerQuery->execute([$id]);
    $partnerName = $getPartnerQuery->fetchColumn();

    if (!$partnerName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus."]);
        exit;
    }

    $softDeletePartnerQuery = $koneksi->prepare("UPDATE partners SET deleted_at = NOW() WHERE id = ?");
    $isDeleted = $softDeletePartnerQuery->execute([$id]);

    $softDeleteVehicleLoansQuery = $koneksi->prepare("UPDATE vehicle_loans SET deleted_by_partner_at = NOW() WHERE partner_id = ?");
    $softDeleteVehicleLoansQuery->execute([$id]);

    if ($isDeleted) {
        echo json_encode(['success' => true, 'message' => "Partner <strong>" . htmlspecialchars($partnerName) . "</strong> berhasil dihapus sementara."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat menghapus partner."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
