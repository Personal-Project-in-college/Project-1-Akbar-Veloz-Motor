<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Hapus vehicle secara permanen jika memang sudah dihapus sebelumnya
    $data = $koneksi->prepare("DELETE FROM vehicles WHERE id = ? AND deleted_at IS NOT NULL OR deleted_by_branch_at IS NOT NULL");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
