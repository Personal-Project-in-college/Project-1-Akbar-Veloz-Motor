<?php
include '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
    // ⬇️ Restore vehicle document
    $data = $koneksi->prepare("UPDATE vehicle_loans SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);
}

header('Location: delete.php');
exit;
