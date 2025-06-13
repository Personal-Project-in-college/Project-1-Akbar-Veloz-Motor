<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getDeleteBrandQuery = $koneksi->prepare("SELECT * FROM brands WHERE deleted_at IS NOT NULL AND name LIKE ? ORDER BY name DESC");
$getDeleteBrandQuery->execute([$keyword]);
$data = $getDeleteBrandQuery->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['order_id']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['vehicle_id']}</td>
                <td>{$row['vehicle_model_name']}</td>
                <td>{$row['date_order']}</td>
                <td>{$row['status']}</td>
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
    echo "<tr><td colspan='3' class='text-center text-danger'>Tidak ada data merek terhapus.</td></tr>";
}
