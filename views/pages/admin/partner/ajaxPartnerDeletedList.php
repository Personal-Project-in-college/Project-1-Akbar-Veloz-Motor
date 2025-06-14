<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getDeletePartnerQuery = $koneksi->prepare("SELECT * FROM partners WHERE deleted_at IS NOT NULL AND name LIKE ? ORDER BY name DESC");
$getDeletePartnerQuery->execute([$keyword]);
$data = $getDeletePartnerQuery->fetchAll(PDO::FETCH_ASSOC);

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
                    <button data-id='{$row['id']}' class='btn btn-success btn-sm restore-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-restore'></i>
                    </button>
                    <button data-id='{$row['id']}' class='btn btn-danger btn-sm destroy-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-forever'></i>
                    </button>
                </td>
            </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='7' class='text-center text-danger'>Tidak ada data partner terhapus.</td></tr>";
}
