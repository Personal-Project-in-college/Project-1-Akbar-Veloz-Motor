<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ⬇️ Restore kendaraan
    $restorePinjamKendaraan = $koneksi->prepare("UPDATE vehicle_loans SET deleted_by_partner_at = NULL WHERE partner_id = ?");
    $restorePinjamKendaraan->execute([$id]);

    // ⬇️ Restore partner
    $data = $koneksi->prepare("UPDATE partners SET deleted_at = NULL WHERE id = ?");
    $data->execute([$id]);
}

header('Location: delete.php');
exit;
