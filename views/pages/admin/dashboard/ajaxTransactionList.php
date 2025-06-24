<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckRole.php';

$sql = "SELECT 
    t.grand_total, t.payment_type, t.payment_method, t.status,
    o.order_date,
    c.name AS customer_name,
    v.id AS vehicle_id,
    vm.name AS model_name,
    b.name AS brand_name,
    t.id
FROM transactions t
JOIN orders o ON t.order_id = o.id
JOIN customers c ON o.customer_id = c.id
JOIN vehicles v ON o.vehicle_id = v.id
JOIN vehicle_models vm ON v.vehicle_model_id = vm.id 
JOIN brands b ON vm.brand_id = b.id
WHERE t.deleted_at IS NULL AND NOT t.status = 'cancelled'";

$sql .= " ORDER BY t.created_at ASC LIMIT 5";

$query = $koneksi->prepare($sql);
$query->execute();
$transactions = $query->fetchAll(PDO::FETCH_ASSOC);

// 🔽 Output Tabel
if ($transactions) {
    foreach ($transactions as $row) {
        echo "<tr>
                <td>{$row['order_date']}</td>
                <td>{$row['vehicle_id']} - {$row['brand_name']} {$row['model_name']}</td>
                <td>{$row['customer_name']}</td>";

        echo is_null($row['grand_total'])
            ? "<td>-</td>"
            : "<td>Rp " . number_format($row['grand_total'], 0, ',', '.') . "</td>";

        echo "  <td>{$row['status']}</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
