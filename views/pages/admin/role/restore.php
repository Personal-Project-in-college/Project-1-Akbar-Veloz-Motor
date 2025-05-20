<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Restore role
    $data = $koneksi->prepare("UPDATE roles SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
