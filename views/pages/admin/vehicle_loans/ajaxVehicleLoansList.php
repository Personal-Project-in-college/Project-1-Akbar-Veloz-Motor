<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$stmt = $koneksi->prepare("SELECT vehicle_loans.*, partners.name AS partner_name, users.name AS user_name FROM vehicle_loans LEFT JOIN partners ON vehicle_loans.partner_id = partners.id LEFT JOIN users ON vehicle_loans.user_id = users.id WHERE vehicle_loans.deleted_at IS NULL AND vehicle_loans.deleted_by_partner_at IS NULL AND partners.name LIKE ? ORDER BY partners.name ASC");
$stmt->execute([$keyword]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        $status = $row['status'] === 'borrowed' ? 'Dipinjam' : 'Dikembalikan';
        $loanDate = date('d M Y, H:i', strtotime($row['loan_date']));
        $returnDate = $row['return_date'] ? date('d M Y, H:i', strtotime($row['return_date'])) : '-';

        echo "<tr>
                <td><a href='../vehicles/edit.php?id={$row['vehicle_id']}'>{$row['vehicle_id']}</a></td>
                <td><a href='../partner/detail.php?id={$row['partner_id']}'>{$row['partner_name']}</a></td>
                <td>{$loanDate}</td>
                <td>{$returnDate}</td>
                <td>{$status}</td>
                <td>
                    <button class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' data-bs-toggle='modal' data-bs-target='#modalShowNote'
                        style='width: 28px; height: 28px; border-radius: 4px;' data-note='{$row['note']}'>
                        <i class='mdi mdi-file-eye'></i>
                    </button>
                </td>
                <td style='display: flex; align-items: center; gap: 8px;'>
                    <a href='detail.php?id={$row['id']}' title='Detail' class='btn btn-secondary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color:  white'>
                        <i class='mdi mdi-eye'></i>
                    </a>
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
    echo "<tr><td colspan='8' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
