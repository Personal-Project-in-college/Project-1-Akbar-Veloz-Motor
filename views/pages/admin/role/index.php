<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<!DOCTYPE html>
<html>

<head>
    <title>Data Role Showroom</title>
</head>

<body>

    <h2>Data Role</h2>
    <a href="create.php">Tambah Role</a><br><br>
    <!-- 🕹️ Tombol untuk masuk ke halaman tambah role -->

    <a href="../../../../index.php">Kembali</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->
    
    <a href="delete.php">Lihat Data Terhapus</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>

        <?php
        // 🪢 Ambil data role dari database yang belum dihapus (deleted_at NULL) dan diurutkan data terlama yang dibuat
        $data = $koneksi->query("SELECT * FROM roles WHERE deleted_at IS NULL ORDER BY created_at ASC");

        // 🔁 Inisialisasi nomor urut
        $no = 1;

        // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
        foreach ($data as $row) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['name']}</td>
                    <td>
                        <!-- Link untuk edit berdasarkan Id dan hapus berdasarkan ID -->
                        <a href='edit.php?id={$row['id']}'>Edit</a> |
                        <a href='softDelete.php?id={$row['id']}'>Hapus</a>
                    </td>
                </tr>";
                $no++; // ➕ Tambah nomor di tiap loop
        }
        ?>
    </table>

</body>

</html>