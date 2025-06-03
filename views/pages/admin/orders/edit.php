<?php
include '../../../../config/koneksi.php';

// Ambil ID dari URL
$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header("Location: index.php");
    exit;
}

// Ambil data order berdasarkan ID
$stmt = $koneksi->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil kendaraan yang available + kendaraan yang sedang digunakan order ini
$vehicle_id = $order['vehicle_id'];
$stmt = $koneksi->prepare("SELECT id FROM vehicles WHERE status = 'available' OR id = ?");
$stmt->execute([$vehicle_id]);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $vehicle_id = $_POST['vehicle_id'];
    $date_order = $_POST['date_order'];
    $status = $_POST['status'];

    // Update orders
    $update = $koneksi->prepare("UPDATE orders SET name = ?, phone = ?, address = ?, vehicle_id = ?, date_order = ?, status = ? WHERE id = ?");
    $update->execute([$name, $phone, $address, $vehicle_id, $date_order, $status, $order_id]);

    // Kalau status test_driver dan belum ada di test_drivers, insert
    if ($status === 'test_driver') {
        $cek = $koneksi->prepare("SELECT COUNT(*) FROM test_drivers WHERE order_id = ?");
        $cek->execute([$order_id]);
        if ($cek->fetchColumn() == 0) {
            $user_id = 1; // nanti ambil dari session login
            $created_at = date('Y-m-d H:i:s');
            $koneksi->prepare("INSERT INTO test_drivers (order_id, user_id, created_at) VALUES (?, ?, ?)")
                ->execute([$order_id, $user_id, $created_at]);
        }
        // Update status kendaraan
        $koneksi->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?")
            ->execute([$vehicle_id]);
    }
    // Update status kendaraan sesuai status order
    if ($status === 'cancel') {
        $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?")
            ->execute([$vehicle_id]);
    } elseif ($status === 'transaction') {
        $koneksi->prepare("UPDATE vehicles SET status = 'transaction' WHERE id = ?")
            ->execute([$vehicle_id]);

        // INSERT ke transactions
        $user_id = 1; // nanti ambil dari session
        $created_at = date('Y-m-d H:i:s');
        $insert = $koneksi->prepare("INSERT INTO transactions (order_id, user_id, grandtotal, payment, `change`, paymentproof, created_at) VALUES (?, ?, 0, 0, 0, '', ?)");
        $insert->execute([$order_id, $user_id, $created_at]);

        // Ambil ID terakhir transaksi yang baru dibuat
        $transaction_id = $koneksi->lastInsertId();

        // Redirect ke halaman transaction
        header("Location: transaction.php?id=$transaction_id");
        exit;
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Order</title></head>
<body>
<h2>Edit Order</h2>
<form method="POST">
    <label>Nama:</label><br>
    <input type="text" name="name" value="<?=htmlspecialchars($order['name'])?>" required><br><br>

    <label>Telepon:</label><br>
    <input type="text" name="phone" maxlength="12" value="<?=htmlspecialchars($order['phone'])?>" required><br><br>

    <label>Alamat:</label><br>
    <textarea name="address" required><?=htmlspecialchars($order['address'])?></textarea><br><br>

    <label>ID Kendaraan:</label><br>
    <select name="vehicle_id" required>
        <option value="">-- Pilih ID Kendaraan --</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?=$vehicle['id']?>" <?=$vehicle['id'] == $order['vehicle_id'] ? 'selected' : ''?>>
                <?=$vehicle['id']?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Tanggal Order:</label><br>
    <input type="date" name="date_order" value="<?=$order['date_order']?>" required><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="cancel" <?=$order['status'] === 'cancel' ? 'selected' : ''?>>Cancel</option>
        <option value="test_driver" <?=$order['status'] === 'test_driver' ? 'selected' : ''?>>Test Driver</option>
        <option value="transaction" <?=$order['status'] === 'transaction' ? 'selected' : ''?>>Transaction</option>
    </select><br><br>

    <button type="submit">Update</button>
</form>
</body>
</html>
