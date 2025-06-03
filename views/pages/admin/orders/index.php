<?php include '../../../../config/koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head><title>Data Orders</title></head>
<body>
<h2>Data Orders</h2>
<a href="create.php">Tambah Order</a><br><br>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Nama Customer</th>
    <th>Telepon</th>
    <th>Alamat</th>
    <th>Kode Kendaraan</th>
    <th>Tanggal Order</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
<?php
$data = $koneksi->query("SELECT * FROM orders WHERE deleted_at IS NULL ORDER BY created_at DESC");
foreach ($data as $row) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['phone']}</td>
        <td>{$row['address']}</td>
        <td>{$row['vehicle_id']}</td>
        <td>{$row['date_order']}</td>
        <td>{$row['status']}</td>
        <td>
            <a href='edit.php?id={$row['id']}'>Edit</a> |
            <a href='delete.php?id={$row['id']}'>Hapus</a>
        </td>
    </tr>";
}
?>
</table>
</body>
</html>
