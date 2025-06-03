<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Hapus role secara permanen jika memang sudah dihapus sebelumnya
    $data = $koneksi->prepare("DELETE FROM vehicle_documents WHERE id = ? AND deleted_at IS NOT NULL");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
