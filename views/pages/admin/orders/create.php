<?php
include '../../../../config/koneksi.php';

// Ambil data kendaraan yang statusnya 'available'
$vehicles = $koneksi->query("SELECT id FROM vehicles WHERE status = 'available'");

// Saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $vehicle_id = $_POST['vehicle_id'];
    $date_order = $_POST['date_order'];
    $status = $_POST['status'];
    $created_at = date('Y-m-d H:i:s');

    // Insert ke orders
    $insert = $koneksi->prepare("INSERT INTO orders (name, phone, address, vehicle_id, date_order, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$name, $phone, $address, $vehicle_id, $date_order, $status, $created_at]);

    // Ambil ID order yang baru saja dibuat
    $order_id = $koneksi->lastInsertId();

    if ($status === 'test_driver') {
        $user_id = 1; // nanti bisa pakai dari session login
        // Insert ke test_drivers
        $koneksi->prepare("INSERT INTO test_drivers (order_id, user_id, created_at) VALUES (?, ?, ?)")
                ->execute([$order_id, $user_id, $created_at]);

        // Update status kendaraan jadi 'test_driver'
        $koneksi->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?")
                ->execute([$vehicle_id]);
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Tambah Order</title></head>
<body>
<h2>Tambah Order Baru</h2>
<form method="POST">
    <label>Nama:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Telepon:</label><br>
    <input type="text" name="phone" maxlength="12" required><br><br>

    <label>Alamat:</label><br>
    <textarea name="address" required></textarea><br><br>

    <label>ID Kendaraan:</label><br>
    <select name="vehicle_id" required>
        <option value="">-- Pilih ID Kendaraan --</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['id'] ?>"><?= $vehicle['id'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Tanggal Order:</label><br>
    <input type="date" name="date_order" required><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="test_driver">Test Driver</option>
        <option value="transaction">Transaction</option>
    </select><br><br>

    <button type="submit">Simpan</button>
</form>
</body>
</html>
