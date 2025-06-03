<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<!DOCTYPE html>
<html>

<head>
    <title>Data Photo Kendaraan</title>
</head>

<body>
    <h2>Data Photo Kendaraan</h2>
    <a href="create.php">Tambah Photo Kendaraan</a><br><br>
    <!-- 🕹️ Tombol untuk masuk ke halaman tambah Dokumen Kendaraan -->

    <a href="../../../../index.php">Kembali</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

    <a href="delete.php">Lihat Data Terhapus</a><br><br>
    <!-- 🕹️ Tombol untuk ke halaman data yang dihapus -->

    <table border="1" cellpadding="10">
        <tr>
            <th>Vehicle ID</th>
            <th>Photo</th>
            <th>Aksi</th>
        </tr>
        
        <?php
        // 🪢 Ambil data Photo Kendaraan dari database yang belum dihapus (deleted_at NULL) dan diurutkan data terlama yang dibuat
        $data = $koneksi->query("SELECT * FROM vehicle_photos WHERE deleted_at IS NULL AND deleted_by_vehicle_at IS NULL ORDER BY vehicle_id ASC");

        // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
        foreach ($data as $row) {
            echo "<tr>
                    <td><a href='../vehicles/edit.php?id={$row['vehicle_id']}'>{$row['vehicle_id']}</a></td>
                    <td><img src='../../../../storage/{$row['photo_path']}' alt='Photo Kendaraan' width='100'></td>
                    <td>
                        <a href='edit.php?id={$row['id']}'>Edit</a> |
                        <a href='softDelete.php?id={$row['id']}'>Hapus</a>
                    </td>
                </tr>";
        }        
        ?>
    </table>
</body>

</html>