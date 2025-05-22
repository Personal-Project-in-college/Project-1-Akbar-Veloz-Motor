<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<!DOCTYPE html>
<html>

<head>
    <title>Data User Shoowroom</title>
</head>

<body>
    <h2>Data User Shoowroom</h2>
    <a href="create.php">Tambah User Shoowroom</a><br><br>
    <!-- 🕹️ Tombol untuk masuk ke halaman tambah User Shoowroom -->

    <a href="../../../../index.php">Kembali</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

    <a href="delete.php">Lihat Data Terhapus</a><br><br>
    <!-- 🕹️ Tombol untuk ke halaman data yang dihapus -->

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Jabatan</th>
            <th>Aksi</th>
        </tr>

        <?php
        // 🪢 Ambil data User Shoowroom dari database yang belum dihapus (deleted_at NULL) dan diurutkan data berdasarkan namanya
        $data = $koneksi->query("SELECT users.*, roles.name AS role_name FROM users LEFT JOIN roles ON users.role_id = roles.id WHERE users.deleted_at IS NULL ORDER BY users.name ASC");

        // 🔁 Inisialisasi nomor urut
        $no = 1;

        // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
        foreach ($data as $row) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['address']}</td>
                    <td>{$row['role_name']}</td>
                    <td>
                        <a href='edit.php?slug={$row['slug']}'>Edit</a> |
                        <a href='softDelete.php?id={$row['id']}'>Hapus</a>
                    </td>
                </tr>";
                $no++; // ➕ Tambah nomor di tiap loop
        }
        ?>
    </table>
</body>

</html>