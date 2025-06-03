<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<h2>Data Vehicle Photo Terhapus</h2>
<a href="index.php">Kembali ke Data Aktif</a><br><br>
<!-- 🕹️ Tombol navigasi untuk kembali ke halaman data Vehicle Photo yang belum dihapus -->

<table border="1" cellpadding="10">
    <tr>
        <th>Vehicle ID</th>
        <th>Photo</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    // 🪢 Ambil semua data photo kendaraan yang sudah di-*soft delete* (deleted_at TIDAK NULL)
    $data = $koneksi->query("SELECT * FROM vehicle_photos WHERE deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL ORDER BY vehicle_id ASC");
    

    // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
    foreach ($data as $row) {
        echo "<tr>
                <td><a href='../vehicles/edit.php?id={$row['vehicle_id']}'>{$row['vehicle_id']}</a></td>
                <td><img src='../../../../storage/{$row['photo_path']}' alt='Photo Kendaraan' width='100'></td>
                <td>" . ($row['deleted_by_vehicle_at'] ? 'Kendaraan telah dihapus' : 'Kendaraan Tersedia') . "</td>
                    <td>
                        ". ($row['deleted_by_vehicle_at'] ? "<a href='edit.php?id={$row['id']}'>Edit</a> | " : "<a href='restore.php?id={$row['id']}'>Restore</a> | ") ." 
                        <a href='destroy.php?id={$row['id']}'>Hapus Permanen</a>
                    </td>
            </tr>";
    }
    ?>
</table>