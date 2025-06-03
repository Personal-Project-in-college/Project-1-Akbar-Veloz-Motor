<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<h2>Data Vehicle Document Terhapus</h2>
<a href="index.php">Kembali ke Data Aktif</a><br><br>
<!-- 🕹️ Tombol navigasi untuk kembali ke halaman data Vehicle Document yang belum dihapus -->

<table border="1" cellpadding="10">
    <tr>
        <th>Vehicle ID</th>
        <th>Partner ID</th>
        <th>User ID</th>
        <th>Tanggal Pinjam</th>
        <th>Tanggal Dikembalikan</th>
        <th>Tujuan Peminjaman</th>
        <th>Status</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    // 🪢 Ambil semua data dokumen kendaraan yang sudah di-*soft delete* (deleted_at TIDAK NULL)
    $data = $koneksi->query("SELECT vehicle_loans.*, partners.name AS partner_name, users.name AS user_name FROM vehicle_loans LEFT JOIN partners ON vehicle_loans.partner_id = partners.id LEFT JOIN users ON vehicle_loans.user_id = users.id WHERE vehicle_loans.deleted_at IS NOT NULL OR vehicle_loans.deleted_by_partner_at IS NOT NULL ORDER BY vehicle_loans.vehicle_id ASC");

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
                <td>" . ($row['deleted_by_partner_at'] ? 'Partner telah dihapus' : '-') . "</td>
                    <td>
                        ". ($row['deleted_by_partner_at'] ? "<a href='edit.php?id={$row['id']}'>Edit</a> | " : "<a href='restore.php?id={$row['id']}'>Restore</a> | ") ." 
                        <a href='destroy.php?id={$row['id']}'>Hapus Permanen</a>
                    </td>
            </tr>";
    }
    ?>
</table>