<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Restore role
    $data = $koneksi->prepare("UPDATE roles SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);

    // ⬇️ Restore user yang deleted_at nya null
    $restoreUser = $koneksi->prepare("UPDATE users SET deleted_by_role_at = NULL WHERE role_id = ?");
    $restoreUser->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
