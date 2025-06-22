<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckRole.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

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
WHERE t.deleted_at IS NULL AND NOT t.status = 'cancelled'
AND (
    c.name LIKE :keyword 
    OR b.name LIKE :keyword 
    OR vm.name LIKE :keyword
)";

// 🔽 Tambahkan filter tanggal jika tersedia
$params = ['keyword' => $keyword];

if ($startDate && $endDate) {
    $sql .= " AND DATE(o.order_date) BETWEEN :start_date AND :end_date";
    $params['start_date'] = $startDate;
    $params['end_date'] = $endDate;
}

$sql .= " ORDER BY t.created_at ASC";

$query = $koneksi->prepare($sql);
$query->execute($params);
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

        echo "<td>{$row['payment_type']}</td>
                <td>{$row['payment_method']}</td>
                <td>{$row['status']}</td>
                <td style='display: flex; align-items: center; gap: 8px;'>
                    <a href='detail.php?id={$row['id']}' title='Detail' class='btn btn-secondary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-text-box'></i>
                    </a>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
