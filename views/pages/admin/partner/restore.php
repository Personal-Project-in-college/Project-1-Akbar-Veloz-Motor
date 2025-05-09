<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Restore partner
    $data = $koneksi->prepare("UPDATE partners SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
