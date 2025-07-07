<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getBankQuery = $koneksi->prepare("SELECT * FROM banks WHERE deleted_at IS NULL AND account_name LIKE ? ORDER BY bank_name ASC");
$getBankQuery->execute([$keyword]);
$data = $getBankQuery->fetchAll(PDO::FETCH_ASSOC);

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
                    <a href='edit.php?id={$row['id']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                        <i class='mdi mdi-pencil'></i>
                    </a>
                    <button data-id='{$row['id']}' class='btn btn-danger btn-sm delete-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-restore'></i>
                    </button>
                </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='6' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
