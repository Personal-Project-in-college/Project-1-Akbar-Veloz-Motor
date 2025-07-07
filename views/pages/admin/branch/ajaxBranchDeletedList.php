<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckRole.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getDeleteBranchQuery = $koneksi->prepare("SELECT * FROM branches WHERE deleted_at IS NOT NULL AND (name LIKE ? OR address LIKE ?) ORDER BY deleted_at DESC");
$getDeleteBranchQuery->execute([$keyword, $keyword]);
$data = $getDeleteBranchQuery->fetchAll(PDO::FETCH_ASSOC);

$no = 1;
if ($data) {
    foreach ($data as $row) {
        $address = htmlspecialchars($row['address']);
        // Memotong teks alamat jika lebih dari 30 karakter untuk tampilan di tabel.
        $shortAddress = substr($row['address'], 0, 30) . (strlen($row['address']) > 30 ? "..." : "");
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['name']}</td>
                <td data-bs-toggle='tooltip' data-bs-placement='top' title='{$address}'>
                    {$shortAddress}
                </td>
                <td style='display: flex; align-items: center; gap: 8px;'>";
                    if (hasAnyRole(['Owner'])) {
                        echo "
                        <a href='detail.php?slug={$row['slug']}' title='Detail' class='btn btn-secondary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                            <i class='mdi mdi-eye'></i>
                        </a>
                        <button data-id='{$row['id']}' class='btn btn-success btn-sm restore-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                            <i class='mdi mdi-restore'></i>
                        </button>
                        <button data-id='{$row['id']}' class='btn btn-danger btn-sm destroy-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                            <i class='mdi mdi-delete-forever'></i>
                        </button>";
                        }
                echo "
                    </td>
            </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='4' class='text-center text-danger'>Tidak ada data cabang terhapus.</td></tr>";
}
