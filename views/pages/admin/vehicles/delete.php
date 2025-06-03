<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<h2>Data Kendaraan Terhapus</h2>
<a href="index.php">Kembali ke Data Aktif</a><br><br>
<!-- 🕹️ Tombol navigasi untuk kembali ke halaman data branch yang belum dihapus -->

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>Kode</th>
        <th>Brand Model</th>
        <th>Type Vehicle</th>
        <th>Color</th>
        <th>Production Year</th>
        <th>Serial Number</th>
        <th>STNK Deadline</th>
        <th>Kilometer</th>
        <th>CC Engine</th>
        <th>Description</th>
        <th>Price</th>
        <th>Status</th>
        <th>Branch</th>
        <th>Aksi</th>
    </tr>

    <?php
    // 🪢 Ambil semua data branch yang sudah di-*soft delete* (deleted_at TIDAK NULL)
    $data = $koneksi->query("SELECT vehicles.*, branches.name AS branch_name FROM vehicles LEFT JOIN branches ON vehicles.branch_id = branches.id WHERE vehicles.deleted_at IS NOT NULL OR vehicles.deleted_by_branch_at IS NOT NULL ORDER BY id ASC");
    
    // 🔁 Inisialisasi nomor urut
    $no = 1;

    // ♾️ Loop untuk tampilkan tiap data dalam bentuk baris tabel
    foreach ($data as $row) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['id']}</td>
                <td>{$row['brand_model']}</td>
                <td>{$row['type_vehicle']}</td>
                <td>{$row['color']}</td>
                <td>{$row['production_year']}</td>
                <td>{$row['serial_number']}</td>
                <td>{$row['stnk_deadline']}</td>
                <td>{$row['kilometer']}</td>
                <td>{$row['cc_engine']}</td>
                <td>{$row['description']}</td>
                <td>{$row['price']}</td>
                <td>{$row['status']}</td>
                <td>" . ($row['deleted_by_branch_at'] ? 'Branch telah dihapus' : $row['branch_name']) . "</td>
                <td>
                    ". ($row['deleted_by_branch_at'] ? "<a href='edit.php?id={$row['id']}'>Edit</a> | " : "<a href='restore.php?id={$row['id']}'>Restore</a> | ") ." 
                    <a href='destroy.php?id={$row['id']}'>Hapus Permanen</a>
                </td>
            </tr>";
        $no++; // ➕ Tambahkan nomor untuk baris berikutnya
    }
    ?>
</table>