<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Hapus Kendaraan Dipinjam secara permanen jika memang sudah dihapus sebelumnya
    $hapusPinjamKendaraan = $koneksi->prepare("DELETE FROM vehicle_loans WHERE partner_id = ? AND deleted_by_partner_at IS NOT NULL");
    $hapusPinjamKendaraan->execute([$_GET['id']]);

    // ⬇️ Hapus partner secara permanen jika memang sudah dihapus sebelumnya
    $data = $koneksi->prepare("DELETE FROM partners WHERE id = ? AND deleted_at IS NOT NULL");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
