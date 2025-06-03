<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<!DOCTYPE html>
<html>

<head>
    <title>Data Peminjaman Kendaraan</title>
</head>

<body>
    <h2>Data Peminjaman Kendaraan</h2>
    <a href="create.php">Tambah Peminjaman Kendaraan</a><br><br>
    <!-- 🕹️ Tombol untuk masuk ke halaman tambah Peminjaman Kendaraan -->

    <a href="../../../../index.php">Kembali</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

    <a href="delete.php">Lihat Data Terhapus</a><br><br>
    <!-- 🕹️ Tombol untuk ke halaman data yang dihapus -->

    <table border="1" cellpadding="10">
        <tr>
            <th>Vehicle ID</th>
            <th>Partner ID</th>
            <th>User ID</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Dikembalikan</th>
            <th>Tujuan Peminjaman</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        
        <?php
        // 🪢 Ambil data Dokumen Kendaraan dari database yang belum dihapus (deleted_at NULL) dan diurutkan data terlama yang dibuat
        $data = $koneksi->query("SELECT vehicle_loans.*, partners.name AS partner_name, users.name AS user_name FROM vehicle_loans LEFT JOIN partners ON vehicle_loans.partner_id = partners.id LEFT JOIN users ON vehicle_loans.user_id = users.id WHERE vehicle_loans.deleted_at IS NULL AND vehicle_loans.deleted_by_partner_at IS NULL ORDER BY vehicle_loans.vehicle_id ASC");

        
        // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
        foreach ($data as $row) {
            echo "<tr>
                    <td><a href='../vehicles/edit.php?id={$row['vehicle_id']}'>{$row['vehicle_id']}</a></td>
                    <td>{$row['partner_name']}</td>
                    <td>{$row['user_name']}</td>
                    <td>{$row['loan_date']}</td>
                    <td>{$row['return_date']}</td>
                    <td>{$row['reason']}</td>
                    <td>{$row['status']}</td>
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