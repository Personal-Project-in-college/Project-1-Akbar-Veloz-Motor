<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getPartnerQuery = $koneksi->prepare("SELECT * FROM partners WHERE deleted_at IS NULL AND name LIKE ? ORDER BY created_at ASC");
$getPartnerQuery->execute([$keyword]);
$data = $getPartnerQuery->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['name']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['email']}</td>
                <td>
                    <button class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' data-bs-toggle='modal' data-bs-target='#modalShowKtp'
                        style='width: 28px; height: 28px; border-radius: 4px;' data-ktp='../../../../../storage/{$row['ktp_scan']}'>
                        <i class='mdi mdi-file-eye'></i>
                    </button>
                </td>
                <td>
                    <button class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' data-bs-toggle='modal' data-bs-target='#modalShowPhoto'
                        style='width: 28px; height: 28px; border-radius: 4px;' data-photo='../../../../../storage/{$row['photo']}'>
                        <i class='mdi mdi-file-eye'></i>
                    </button>
                </td>
                <td style='display: flex; align-items: center; gap: 8px;'>
                    <a href='detail.php?id={$row['id']}' title='Detail' class='btn btn-secondary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-eye'></i>
                    </a>
                    <a href='edit.php?id={$row['id']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                        <i class='mdi mdi-pencil'></i>
                    </a>
                    <button data-id='{$row['id']}' class='btn btn-danger btn-sm delete-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-restore'></i>
                    </button>
                </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='7' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
?>

<div></div>