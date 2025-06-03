<?php
include '../../../../config/koneksi.php';

$transaction_id = $_GET['id'] ?? null;
if (!$transaction_id) {
    header("Location: index.php");
    exit;
}

// Ambil data transaksi
$stmt = $koneksi->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->execute([$transaction_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$transaction) {
    echo "Transaksi tidak ditemukan.";
    exit;
}

// Ambil data order (relasi ke transaction)
$order_stmt = $koneksi->prepare("
    SELECT orders.*, orders.name AS customer_name, orders.phone, orders.address, vehicles.price
    FROM orders
    JOIN vehicles ON orders.vehicle_id = vehicles.id
    WHERE orders.id = ?
");
$order_stmt->execute([$transaction['order_id']]);
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    echo "Order tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grandtotal = $_POST['grandtotal'];
    $payment = $_POST['payment'];
    $change = $payment - $grandtotal;
    $updated_at = date('Y-m-d H:i:s');

    // Upload bukti pembayaran
    $paymentproof = '';
    if (isset($_FILES['paymentproof']) && $_FILES['paymentproof']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['paymentproof']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('proof_') . '.' . $ext;
        move_uploaded_file($_FILES['paymentproof']['tmp_name'], '../../../../storage/paymentproof/' . $filename);
        $paymentproof = $filename;
    }

    // Update transaksi
    $update = $koneksi->prepare("UPDATE transactions SET grandtotal = ?, payment = ?, `change` = ?, paymentproof = ?, updated_at = ? WHERE id = ?");
    $update->execute([$grandtotal, $payment, $change, $paymentproof, $updated_at, $transaction_id]);

    // ✅ Update status di table orders ke "finish"
    $koneksi->prepare("UPDATE orders SET status = 'finish' WHERE id = ?")
        ->execute([$transaction['order_id']]);

    // ✅ Update status di table vehicles ke "sold"
    $koneksi->prepare("UPDATE vehicles SET status = 'sold' WHERE id = ?")
        ->execute([$order['vehicle_id']]);

    echo "<script>alert('Pembayaran berhasil disimpan!');window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Input Pembayaran</title>
</head>

<body>
    <h2>Form Pembayaran</h2>

    <!-- Tampilkan informasi pelanggan dan kendaraan -->
    <p><strong>Nama Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>No. Telepon:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p><strong>Alamat:</strong> <?= htmlspecialchars($order['address']) ?></p>
    <p><strong>Vehicle ID:</strong> <?= htmlspecialchars($order['vehicle_id']) ?></p>

    <form method="POST" enctype="multipart/form-data">
        <label>Total Belanja (Grand Total):</label><br>
        <input type="number" name="grandtotal" value="<?= htmlspecialchars($order['price']) ?>" required readonly><br><br>

        <label>Uang Pembayaran:</label><br>
        <input type="number" name="payment" id="payment" required><br><br>

        <label>Kembalian:</label><br>
        <input type="number" id="change" readonly><br><br>


        <label>Bukti Pembayaran (Upload):</label><br>
        <input type="file" name="paymentproof" accept="image/*" required><br><br>

        <button type="submit">Simpan Pembayaran</button>
    </form>
    <script>
        const grandTotal = <?= (int)$order['price']; ?>;
        const paymentInput = document.getElementById('payment');
        const changeInput = document.getElementById('change');

        paymentInput.addEventListener('input', function() {
            const payment = parseInt(paymentInput.value) || 0;
            const change = payment - grandTotal;
            changeInput.value = change >= 0 ? change : 0;
        });
    </script>

</body>

</html>