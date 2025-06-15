<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$stmt = $koneksi->prepare("SELECT vehicles.*, branches.name AS branch_name, vehicle_models.name AS model_name, brands.name AS brand_name FROM vehicles LEFT JOIN branches ON vehicles.branch_id = branches.id LEFT JOIN vehicle_models ON vehicles.vehicle_model_id = vehicle_models.id LEFT JOIN brands ON vehicle_models.brand_id = brands.id WHERE (vehicles.deleted_at IS NOT NULL OR vehicles.deleted_by_branch_at IS NOT NULL) AND vehicles.id LIKE ? ORDER BY COALESCE(vehicles.deleted_at, vehicles.deleted_by_branch_at) DESC");
$stmt->execute([$keyword]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        $typeVehicle = $row['type_vehicle'] === 'motorcycle' ? 'Motor' : 'Mobil';

        switch ($row['status']) {
            case 'available':
                $statusText = 'Tersedia';
                $statusColor = '#7B8255';
                break;
            case 'service':
                $statusText = 'Service';
                $statusColor = '#FA7D09';
                break;
            case 'test_drive':
                $statusText = 'Tes Jalan';
                $statusColor = '#838ABF';
                break;
            case 'sold':
                $statusText = 'Terjual';
                $statusColor = '#D29A18';
                break;
            default: // Jika status tidak dikenali, tampilkan apa adanya.
                $statusText = ucfirst($row['status']);
                $statusColor = '#6c757d';
                break;
        }

        $stnkText = 'Data Tidak Valid';
        $stnkColor = '#6c757d'; // Default color
        if (!empty($row['stnk_deadline'])) {
            try {
                $stnkDate = new DateTime($row['stnk_deadline']);
                $today = new DateTime();
                $isExpired = $stnkDate < $today;
                $interval = $today->diff($stnkDate);

                if ($isExpired) {
                    $stnkText = "Kadaluarsa!";
                    $stnkColor = '#ACB3B5'; // Abu-abu untuk kadaluarsa
                } elseif ($interval->y >= 1) {
                    $stnkText = "{$interval->y} thn+";
                    $stnkColor = 'black'; // Hitam untuk > 1 tahun
                } elseif ($interval->m >= 1) {
                    $stnkText = "{$interval->m} bln";
                    $stnkColor = '#CB7A01'; // Oranye untuk beberapa bulan
                } else {
                    $stnkText = "{$interval->d} hr";
                    $stnkColor = '#FF0000'; // Merah untuk beberapa hari
                }
            } catch (Exception $e) {
                // Biarkan default jika tanggal STNK tidak valid
            }
        }

        $formattedPrice = "Rp " . number_format($row['price_displayed'], 0, ',', '.');
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['brand_name'] . ' ' . $row['model_name']) . "</td>
                <td>" . htmlspecialchars($typeVehicle) . "</td>
                <td style='color: {$stnkColor}; font-weight: bold;'>" . htmlspecialchars($stnkText) . "</td>
                <td>" . htmlspecialchars($formattedPrice) . "</td>
                <td><span class='badge' style='background-color: {$statusColor}; color: white;'>" . htmlspecialchars($statusText) . "</span></td>
                <td>" . ($row['deleted_by_branch_at'] ? 'Branch telah dihapus' : $row['branch_name']) . "</td>
                <td style='display: flex; align-items: center; gap: 8px;'>"
            . ($row['deleted_by_branch_at'] ?
                "<a href='edit.php?id={$row['id']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' 
                            style='width: 28px; height: 28px; border-radius: 4px;'>
                            <i class='mdi mdi-pencil'></i>
                        </a>" :
                "<button data-id='{$row['id']}' class='btn btn-success btn-sm restore-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                            <i class='mdi mdi-restore'></i>
                        </button>") .
            "<button data-id='{$row['id']}' class='btn btn-danger btn-sm destroy-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-forever'></i>
                    </button>
                </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-danger'>Tidak ada data kendaraan terhapus.</td></tr>";
}
