<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getDeleteBrandQuery = $koneksi->prepare("
    SELECT  
        o.id AS order_id, 
        o.date_order, 
        o.type_order, 
        o.deleted_at, 
        c.name AS customer_name, 
        c.email AS customer_email, 
        c.phone AS customer_phone, 
        v.id AS vehicle_id, 
        vm.name AS vehicle_model_name 
    FROM orders AS o 
    JOIN customers AS c ON o.customer_id = c.id 
    JOIN vehicles AS v ON o.vehicle_id = v.id 
    JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id 
    WHERE o.deleted_at IS NOT NULL AND c.name LIKE ? 
    ORDER BY o.created_at DESC
");
$getDeleteBrandQuery->execute([$keyword]);
$data = $getDeleteBrandQuery->fetchAll(PDO::FETCH_ASSOC);

$currentDateTime = new DateTime();
$no = 1;

if ($data) {
    foreach ($data as $row) {
        $statusReal = '-';
        if ($row['type_order'] === 'transaction') {
            $stmt = $koneksi->prepare("SELECT status FROM transactions WHERE order_id = ?");
            $stmt->execute([$row['order_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $statusReal = $result['status'] ?? '-';
        } elseif ($row['type_order'] === 'test_driver') {
            $stmt = $koneksi->prepare("SELECT status FROM test_drivers WHERE order_id = ?");
            $stmt->execute([$row['order_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $statusReal = $result['status'] ?? '-';
        }


        $vehicleInfo = "{$row['vehicle_id']} - {$row['vehicle_model_name']}";
        $deletedAt = new DateTime($row['deleted_at']);
        $interval = $currentDateTime->getTimestamp() - $deletedAt->getTimestamp();
        $canRestore = $interval <= 3600; // 1 jam = 3600 detik

        echo "<tr>
                <td>{$no}</td>
                <td>{$row['customer_name']}</td>
                <td>{$vehicleInfo}</td>
                <td>{$row['date_order']}</td>
                <td>{$row['type_order']}</td>
                <td>{$statusReal}</td>
                <td style='display: flex; align-items: center; gap: 8px;'>";

        if ($canRestore) {
            echo "<button data-id='{$row['order_id']}' class='btn btn-success btn-sm restore-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                    <i class='mdi mdi-restore'></i>
                  </button>";
        }

        echo "<button data-id='{$row['order_id']}' class='btn btn-danger btn-sm destroy-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                <i class='mdi mdi-delete-forever'></i>
              </button>
              </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-danger'>Tidak ada data order terhapus.</td></tr>";
}
