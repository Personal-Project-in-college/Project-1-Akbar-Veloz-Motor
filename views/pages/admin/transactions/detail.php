<?php
include '../../../../config/koneksi.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID tidak valid");
}

// Ambil data transaksi
$stmt = $koneksi->prepare("SELECT t.*, o.name AS customer_name, p.name AS partner_name FROM transactions t
    LEFT JOIN orders o ON t.order_id = o.id
    LEFT JOIN partners p ON t.partner_id = p.id
    WHERE t.id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Transaksi tidak ditemukan");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Transaksi</title>
</head>
<body>
<h2>🧾 Detail Transaksi #<?=$data['id']?></h2>

<table cellpadding="5">
<?php if (!empty($data['customer_name'])): ?>
    <tr><td><strong>Customer</strong></td><td><?=$data['customer_name']?></td></tr>
    <?php elseif (!empty($data['partner_name'])): ?>
        <tr><td><strong>Partner</strong></td><td><?=$data['partner_name']?></td></tr>
    <?php else: ?>
        <tr><td><strong>Customer/Partner</strong></td><td>-</td></tr>
    <?php endif?>
    <tr><td><strong>Grand Total</strong></td><td>Rp<?=number_format($data['grandtotal'])?></td></tr>
    <tr><td><strong>Payment</strong></td><td>Rp<?=number_format($data['payment'])?></td></tr>
    <tr><td><strong>Kembalian</strong></td><td>Rp<?=number_format($data['change'])?></td></tr>
    <tr><td><strong>Tanggal</strong></td><td><?=$data['created_at']?></td></tr>
    <tr>
        <td><strong>Bukti Pembayaran</strong></td>
        <td>
            <?php if ($data['paymentproof']): ?>
                <img src="../../../../storage/paymentproof/<?=$data['paymentproof']?>" width="300" alt="Bukti">
            <?php else: ?>
                <i>Tidak ada bukti pembayaran</i>
            <?php endif?>
        </td>
    </tr>
</table>
</body>
</html>
