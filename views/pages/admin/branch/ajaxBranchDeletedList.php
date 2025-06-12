<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getDeleteBrandQuery = $koneksi->prepare("SELECT * FROM branches WHERE deleted_at IS NOT NULL AND name LIKE ? ORDER BY deleted_at DESC");
$getDeleteBrandQuery->execute([$keyword]);
$data = $getDeleteBrandQuery->fetchAll(PDO::FETCH_ASSOC);

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
    echo "<tr><td colspan='4' class='text-center text-danger'>Tidak ada data cabang terhapus.</td></tr>";
}
