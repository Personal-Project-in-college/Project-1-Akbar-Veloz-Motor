<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

// SELECT pakai kolom type_order
$getOrderQuery = $koneksi->prepare("
    SELECT 
        o.id AS order_id, 
        o.date_order, 
        o.type_order, 
        c.name AS customer_name, 
        c.email AS customer_email, 
        c.phone AS customer_phone, 
        v.id AS vehicle_id, 
        vm.name AS vehicle_model_name 
    FROM orders AS o 
    JOIN customers AS c ON o.customer_id = c.id 
    JOIN vehicles AS v ON o.vehicle_id = v.id 
    JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id 
    WHERE o.deleted_at IS NULL AND c.name LIKE ? 
    ORDER BY o.created_at DESC
");
$getOrderQuery->execute([$keyword]);
$data = $getOrderQuery->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        $vehicleInfo = "{$row['vehicle_id']} - {$row['vehicle_model_name']}";
        $orderId = $row['order_id'];
        $typeOrder = $row['type_order'];

        // Ambil status dari tabel sesuai type_order
        if ($typeOrder === 'test_driver') {
            $statusQuery = $koneksi->prepare("SELECT status FROM test_drivers WHERE order_id = ? LIMIT 1");
        } elseif ($typeOrder === 'transaction') {
            $statusQuery = $koneksi->prepare("SELECT status FROM transactions WHERE order_id = ? LIMIT 1");
        }

        $statusValue = '-';
        if (isset($statusQuery)) {
            $statusQuery->execute([$orderId]);
            $statusResult = $statusQuery->fetchColumn();
            $statusValue = $statusResult ?: '-';
        }

        echo "<tr>
                <td>{$no}</td>
                <td>{$row['customer_name']}</td>
                <td>{$vehicleInfo}</td>
                <td>{$row['date_order']}</td>
                <td>" . ucfirst($typeOrder) . "</td>
                <td>" . ucfirst($statusValue) . "</td>
                <td style='display: flex; align-items: center; gap: 8px;'>
                    <a href='edit.php?id={$orderId}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                        <i class='mdi mdi-pencil'></i>
                    </a>
                    <button data-id='{$orderId}' class='btn btn-danger btn-sm delete-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-restore'></i>
                    </button>
                </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
