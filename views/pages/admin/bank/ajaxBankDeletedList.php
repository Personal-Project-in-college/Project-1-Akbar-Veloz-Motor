<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getDeleteBankQuery = $koneksi->prepare("SELECT * FROM banks WHERE deleted_at IS NOT NULL AND account_name LIKE ? ORDER BY bank_name ASC");
$getDeleteBankQuery->execute([$keyword]);
$data = $getDeleteBankQuery->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        $statusText = $row['is_active'] == 1 ? 'Aktif' : 'Nonaktif';
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['bank_name']}</td>
                <td>{$row['account_number']}</td>
                <td>{$row['account_name']}</td>
                <td>{$statusText}</td>
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
    echo "<tr><td colspan='6' class='text-center text-danger'>Tidak ada data bank terhapus.</td></tr>";
}
