<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Restore user
    $data = $koneksi->prepare("UPDATE users SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
