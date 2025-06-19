<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckRole.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getBranchQuery = $koneksi->prepare("SELECT * FROM branches WHERE deleted_at IS NULL AND (name OR address LIKE ?) ORDER BY created_at ASC");
$getBranchQuery->execute([$keyword]);
$data = $getBranchQuery->fetchAll(PDO::FETCH_ASSOC);
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
                <td style='display: flex; align-items: center; gap: 8px;'>
                    <a href='detail.php?slug={$row['slug']}' title='Detail' class='btn btn-secondary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-eye'></i>
                    </a>";

                        if (hasAnyRole(['Owner'])) {
                            echo "<a href='edit.php?slug={$row['slug']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                                <i class='mdi mdi-pencil'></i>
                            </a>";
                        }

                        echo "<button data-id='{$row['id']}' class='btn btn-danger btn-sm delete-btn d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white'>
                        <i class='mdi mdi-delete-restore'></i>
                    </button>
                </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='4' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
