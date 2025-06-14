<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

// Mapping Bahasa Indonesia
function formatOrderType($type)
{
    return $type === 'test_driver' ? 'Coba Kendaraan' : 'Transaksi';
}

function formatStatus($status)
{
    return match ($status) {
        'proced' => 'Diproses',
        'cancelled' => 'Dibatalkan',
        'finished' => 'Selesai',
        default => ucfirst($status)
    };
}

$getOrderQuery = $koneksi->prepare("
    SELECT 
        o.id AS order_id, 
        o.created_at, 
        o.type_order, 
        o.status AS order_status,
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
        $statusValue = formatStatus($row['order_status']);
        $typeLabel = formatOrderType($typeOrder);
        $orderDate = $row['created_at'] ? date('d M Y, H:i', strtotime($row['created_at'])) : '-';

        $redirectHref = $typeOrder === 'test_driver'
            ? "../test_driver/edit.php?id={$orderId}"
            : "../transactions/transaction.php?id={$orderId}";

        echo "<tr>
                <td>{$no}</td>
                <td>{$row['customer_name']}</td>
                <td>{$vehicleInfo}</td>
                <td>{$orderDate}</td>
                <td>{$typeLabel}</td>
                <td>{$statusValue}</td>
                <td style='display: flex; align-items: center; gap: 8px;'>
                    <a href='{$redirectHref}' title='Lanjut' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                        <i class='mdi mdi-arrow-right-bold-circle'></i>
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
