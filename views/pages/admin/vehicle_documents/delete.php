<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<h2>Data Vehicle Document Terhapus</h2>
<a href="index.php">Kembali ke Data Aktif</a><br><br>
<!-- 🕹️ Tombol navigasi untuk kembali ke halaman data Vehicle Document yang belum dihapus -->

<table border="1" cellpadding="10">
    <tr>
        <th>Vehicle ID</th>
        <th>STNK</th>
        <th>BPKB</th>
        <th>Nota Service</th>
        <th>Nota Pembelian</th>
        <th>Asuransi</th>
        <th>Aksi</th>
    </tr>

    <?php
    // 🪢 Ambil semua data dokumen kendaraan yang sudah di-*soft delete* (deleted_at TIDAK NULL)
    $data = $koneksi->query("SELECT * FROM vehicle_documents WHERE deleted_at IS NOT NULL ORDER BY vehicle_id ASC");

    // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
    foreach ($data as $row) {
        echo "<tr>
                <td><a href='../vehicles/edit.php?id={$row['vehicle_id']}'>{$row['vehicle_id']}</a></td>
                    <td>{$row['stnk']}</td>
                    <td>{$row['bpkb']}</td>
                    <td>{$row['service_note']}</td>
                    <td>{$row['nota']}</td>
                    <td>{$row['asuransi']}</td>
                <td>
                    <!-- Link untuk edit berdasarkan Slug dan hapus berdasarkan ID -->
                    <a href='restore.php?id={$row['id']}'>Restore</a> |
                    <a href='destroy.php?id={$row['id']}'>Hapus Permanen</a>
                </td>
            </tr>";
    }
    ?>
</table>