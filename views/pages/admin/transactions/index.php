<?php
include '../../../../config/koneksi.php';

// Hari ini
$today = date('Y-m-d');
$today_stmt = $koneksi->prepare("SELECT t.*, o.name AS customer_name FROM transactions t JOIN orders o ON t.order_id = o.id WHERE DATE(t.created_at) = ?");
$today_stmt->execute([$today]);
$today_transactions = $today_stmt->fetchAll(PDO::FETCH_ASSOC);

// Bulan terakhir (exclude hari ini)
$month_stmt = $koneksi->prepare("SELECT t.*, o.name AS customer_name FROM transactions t JOIN orders o ON t.order_id = o.id WHERE DATE(t.created_at) < ? AND t.created_at >= DATE_SUB(?, INTERVAL 30 DAY)");
$month_stmt->execute([$today, $today]);
$month_transactions = $month_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Transaksi</title>
</head>
<body>
<h2>🗓️ Transaksi Hari Ini (<?= $today ?>)</h2>
<a href="laporan_harian_pdf.php" target="_blank">📄 Cetak Laporan Harian (PDF)</a>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr><th>No</th><th>Nama Customer</th><th>Total</th><th>Tanggal</th><th>Aksi</th></tr>
    </thead>
    <tbody>
        <?php foreach ($today_transactions as $i => $trx): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($trx['customer_name']) ?></td>
            <td>Rp<?= number_format($trx['grandtotal']) ?></td>
            <td><?= $trx['created_at'] ?></td>
            <td>
                <a href="detail.php?id=<?= $trx['id'] ?>">Detail</a> |
                <a href="cetak-struk.php?id=<?= $trx['id'] ?>" target="_blank">Print Struk</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<br><br>

<h2>🗓️ Transaksi Bulan Ini</h2>
<a href="laporan_bulanan_pdf.php" target="_blank">📄 Cetak Laporan Bulanan (PDF)</a>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr><th>No</th><th>Nama Customer</th><th>Total</th><th>Tanggal</th><th>Aksi</th></tr>
    </thead>
    <tbody>
        <?php foreach ($month_transactions as $i => $trx): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($trx['customer_name']) ?></td>
            <td>Rp<?= number_format($trx['grandtotal']) ?></td>
            <td><?= $trx['created_at'] ?></td>
            <td>
                <a href="detail.php?id=<?= $trx['id'] ?>">Detail</a> |
                <a href="cetak-struk.php?id=<?= $trx['id'] ?>" target="_blank">Print Struk</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
</body>
</html>
