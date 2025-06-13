<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getOrderQuery = $koneksi->prepare("SELECT o.id AS order_id, o.date_order, o.status, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, v.id AS vehicle_id, vm.name AS vehicle_model_name FROM orders AS o JOIN customers AS c ON o.customer_id = c.id JOIN vehicles AS v ON o.vehicle_id = v.id JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id WHERE o.deleted_at IS NULL AND c.name LIKE ? ORDER BY o.created_at DESC");
$getOrderQuery->execute([$keyword]);
$data = $getOrderQuery->fetchAll(PDO::FETCH_ASSOC);

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
                    <a href='edit.php?id={$row['order_id']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                        <i class='mdi mdi-pencil'></i>
                    </a>
                    <button data-id='{$row['order_id']}' class='btn btn-danger btn-sm delete-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-restore'></i>
                    </button>
                </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
