<?php
include '../../../../config/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID tidak valid");

$stmt = $koneksi->prepare("
    SELECT 
        t.*, 
        o.name AS customer_name, o.phone, o.address, 
        v.brand_model, v.type_vehicle, v.color, v.stnk_deadline, v.kilometer, v.cc_engine, v.production_year, v.price,
        u.name AS cashier_name
    FROM transactions t 
    LEFT JOIN orders o ON t.order_id = o.id 
    LEFT JOIN vehicles v ON o.vehicle_id = v.id 
    LEFT JOIN users u ON t.user_id = u.id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) die("Data tidak ditemukan");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran</title>
    <style>
        body { font-family: monospace; }
        .receipt { width: 300px; margin: auto; }
        .text-center { text-align: center; }
        .border-top { border-top: 1px dashed #000; margin: 10px 0; }
    </style>
</head>
<body onload="window.print()">
<div class="receipt">
    <h3 class="text-center">Akbar Veloz Motor</h3>
    <p class="text-center">Jl. Showroom No.1, Subang</p>
    <div class="border-top"></div>
    <p><strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($data['created_at'])) ?></p>
    <p><strong>Customer:</strong> <?= $data['customer_name'] ?? '-' ?></p>
    <p><strong>No. HP:</strong> <?= $data['phone'] ?? '-' ?></p>
    <p><strong>Alamat</strong> <?= $data['address'] ?? '-' ?></p>
    <div class="border-top"></div>
    <p><strong>Kendaraan: (<?= $data['type_vehicle'] ?>)</strong><br>
        <?= $data['brand_model'] ?> | <?= $data['color'] ?> | <?= $data['production_year'] ?></p>
    <p><strong>Kilometer:</strong> <?= $data['kilometer'] ?? '-' ?></p>
    <p><strong>CC Engine:</strong> <?= $data['cc_engine'] ?? '-' ?></p>
    <p><strong>STNK Deadline:</strong> <?= $data['stnk_deadline'] ?? '-' ?></p>
    <p><strong>Harga:</strong> Rp<?= number_format($data['price']) ?></p>
    <div class="border-top"></div>
    <p><strong>Total:</strong> Rp<?= number_format($data['grandtotal']) ?></p>
    <p><strong>Dibayar:</strong> Rp<?= number_format($data['payment']) ?></p>
    <p><strong>Kembalian:</strong> Rp<?= number_format($data['change']) ?></p>
    <p><strong>Kasir:</strong> <?=  $data['cashier_name'] ?? '-' ?></p>
    <div class="border-top"></div>
    <p class="text-center">Terima kasih 🙏</p>
</div>
</body>
</html>
