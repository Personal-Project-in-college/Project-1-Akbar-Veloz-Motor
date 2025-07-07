<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$stmt = $koneksi->prepare("SELECT vehicle_models.*, brands.name AS brand_name FROM vehicle_models JOIN brands ON vehicle_models.brand_id = brands.id WHERE vehicle_models.deleted_at IS NULL AND vehicle_models.deleted_by_brand_at IS NULL AND (brands.name LIKE ? OR vehicle_models.name LIKE ?) ORDER BY brands.name ASC");
$stmt->execute([$keyword, $keyword]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['brand_name']}</td>
                <td>{$row['name']}</td>
                <td style='display: flex; align-items: center; gap: 8px;'>
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
    echo "<tr><td colspan='4' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
