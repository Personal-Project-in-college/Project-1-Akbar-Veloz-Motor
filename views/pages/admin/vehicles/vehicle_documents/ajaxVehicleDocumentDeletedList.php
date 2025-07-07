<?php
include '../../../../../config/koneksi.php';

$id = $_GET['vehicle_id'] ?? null;

$getVehicleDocumentQuery = $koneksi->prepare("SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL) ORDER BY created_at ASC");
$getVehicleDocumentQuery->bindValue(':vehicle_id', $id, PDO::PARAM_STR);
$getVehicleDocumentQuery->execute();
$data = $getVehicleDocumentQuery->fetchAll(PDO::FETCH_ASSOC);

$formatTeks = function ($filePath, $label) {
    if (empty($filePath)) return 'Kosong';
    return "<a href='../../../../storage/" . $filePath . "' target='_blank'>Buka $label</a>";
};

$cekAktif = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = ? AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL");
$cekAktif->execute([$id]);
$adaAktif = $cekAktif->fetchColumn() > 0;

if ($data) {
    foreach ($data as $row) {
        echo "<tr>
                <td>" . $formatTeks($row['stnk'], 'STNK') . "</td>
                <td>" . $formatTeks($row['bpkb'], 'BPKB') . "</td>
                <td>" . $formatTeks($row['service_note'], 'Nota Service') . "</td>
                <td>" . $formatTeks($row['nota'], 'Nota') . "</td>
                <td>" . $formatTeks($row['asuransi'], 'Asuransi') . "</td>
                <td style='display: flex; align-items: center; gap: 8px;'>";

        if (!$adaAktif) {
            echo "<button data-id='{$row['id']}' data-vehicle-id='{$id}' class='btn btn-success btn-sm restore-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-restore'></i>
                    </button>";
        }

        echo "<button data-id='{$row['id']}' data-vehicle-id='{$id}' class='btn btn-danger btn-sm destroy-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-forever'></i>
                    </button>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center text-danger'>Tidak ada data kendaraan dokumen terhapus.</td></tr>";
}
