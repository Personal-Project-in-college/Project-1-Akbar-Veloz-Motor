<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$stmt = $koneksi->prepare("SELECT users.*, roles.name AS role_name FROM users LEFT JOIN roles ON users.role_id = roles.id WHERE users.deleted_at IS NULL AND deleted_by_role_at IS NULL AND users.role_id = 2 AND (users.name LIKE ? OR users.phone LIKE ? OR users.address LIKE ?) ORDER BY users.name ASC");
$stmt->execute([$keyword, $keyword, $keyword]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        $address = htmlspecialchars($row['address']);
        // Memotong teks alamat jika lebih dari 30 karakter untuk tampilan di tabel.
        $shortAddress = substr($row['address'], 0, 30) . (strlen($row['address']) > 30 ? "..." : "");
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['name']}</td>
                <td>{$row['phone']}</td>
                <td>{$shortAddress}</td>
                <td>{$row['role_name']}</td>
                <td style='display: flex; align-items: center; gap: 8px;'>
                    <a href='detail.php?id={$row['id']}' title='Detail' class='btn btn-secondary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
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
    echo "<tr><td colspan='6' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
?>

<div></div>