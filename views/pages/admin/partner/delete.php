<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<h2>Data Partner Terhapus</h2>
<a href="index.php">Kembali ke Data Aktif</a><br><br>
<!-- 🕹️ Tombol navigasi untuk kembali ke halaman data branch yang belum dihapus -->

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Telepon</th>
        <th>Alamat</th>
        <th>Aksi</th>
    </tr>

    <?php
    // 🪢 Ambil semua data branch yang sudah di-*soft delete* (deleted_at TIDAK NULL)
    $data = $koneksi->query("SELECT * FROM partners WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");

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
                    <a href='restore.php?id={$row['id']}'>Restore</a> |
                    <a href='destroy.php?id={$row['id']}'>Hapus Permanen</a>
                </td>
            </tr>";
        $no++; // ➕ Tambahkan nomor untuk baris berikutnya
    }
    ?>
</table>