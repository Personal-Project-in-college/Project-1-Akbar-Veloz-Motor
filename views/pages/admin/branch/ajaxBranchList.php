<?php
include '../../../../config/koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = "%$keyword%";

$getBrandQuery = $koneksi->prepare("SELECT * FROM branches WHERE deleted_at IS NULL AND name LIKE ? ORDER BY created_at ASC");
$getBrandQuery->execute([$keyword]);
$data = $getBrandQuery->fetchAll(PDO::FETCH_ASSOC);

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
                    <a href='edit.php?slug={$row['slug']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
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
    echo "<tr><td colspan='4' class='text-center text-danger'>Data tidak ditemukan.</td></tr>";
}
