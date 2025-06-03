<?php include '../../../../config/koneksi.php'; ?>
<!-- 🔗 Hubungkan ke file koneksi database -->

<!DOCTYPE html>
<html>

<head>
    <title>Data Kendaraan</title>
</head>

<body>
    <h2>Data Kendaraan</h2>
    <a href="create.php">Tambah Data</a><br><br>
    <!-- 🕹️ Tombol untuk masuk ke halaman tambah cabang -->

    <a href="../../../../index.php">Kembali</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

    <a href="delete.php">Lihat Data Terhapus</a><br><br>
    <!-- 🕹️ Tombol untuk kembali ke halaman index -->

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
        // 🔍 Ambil data kendaraan dari tabel 'vehicles' dan gabungkan (join) dengan tabel 'branches'
        // 📎 LEFT JOIN digunakan agar tetap bisa ambil data meskipun ada kemungkinan branch tidak ditemukan
        // 📛 Alias 'branch_name' dipakai agar lebih mudah ditampilkan di tabel HTML
        // ❗️Filter kendaraan yang tidak dihapus (vehicles.deleted_at IS NULL)
        // ❗️Filter juga agar hanya ambil cabang yang belum dihapus (branches.deleted_at IS NULL)
        // 🎯 Tujuan: hanya tampilkan kendaraan yang aktif dan berasal dari cabang yang juga masih aktif
        $data = $koneksi->query("SELECT vehicles.*, branches.name AS branch_name FROM vehicles LEFT JOIN branches ON vehicles.branch_id = branches.id WHERE vehicles.deleted_at IS NULL AND deleted_by_branch_at IS NULL");



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
                    <td>{$row['branch_name']}</td>
                    <td>
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