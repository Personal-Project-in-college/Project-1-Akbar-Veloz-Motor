<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$stmt = $koneksi->prepare("SELECT users.*, roles.name AS role_name FROM users LEFT JOIN roles ON users.role_id = roles.id WHERE (users.deleted_at IS NOT NULL OR deleted_by_role_at IS NOT NULL) AND users.role_id = 2 AND (users.name LIKE ? OR users.phone LIKE ? OR users.address LIKE ?) ORDER BY users.name ASC");
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
    echo "<tr><td colspan='6' class='text-center text-danger'>Tidak ada data karyawan terhapus.</td></tr>";
}
