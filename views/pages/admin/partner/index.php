<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<!DOCTYPE html>
<html>

<head>
    <title>Data Partner</title>
</head>

<body>
    <h2>Data Partner</h2>
    <a href="create.php">Tambah Partner</a><br><br>
    <!-- 🕹️ Tombol untuk masuk ke halaman tambah partner -->

    <a href="../../../../index.php">Kembali</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

    <a href="delete.php">Lihat Data Terhapus</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>

        <?php
        // 🪢 Ambil data partner dari database yang belum dihapus (deleted_at NULL) dan diurutkan data terlama yang dibuat
        $data = $koneksi->query("SELECT * FROM partners WHERE deleted_at IS NULL ORDER BY created_at ASC");

        // 🔁 Inisialisasi nomor urut
        $no = 1;

        // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
        foreach ($data as $row) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['address']}</td>
                    <td>
                        <!-- Link untuk edit berdasarkan Slug dan hapus berdasarkan ID -->
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