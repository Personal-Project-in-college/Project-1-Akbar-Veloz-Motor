<?php
require '../../../../vendor/autoload.php';

use Mpdf\Mpdf;

include '../../../../config/koneksi.php';

$startDate = !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = !empty($_GET['end_date']) ? $_GET['end_date'] : null;

// Helper
function formatRupiah($number)
{
    return 'Rp ' . number_format($number, 0, ',', '.');
}

function translateStatus($status)
{
    return match ($status) {
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Lunas',
        'dp_paid' => 'Lunas DP',
        'failed' => 'Gagal',
        'cancelled' => 'Dibatalkan',
        default => ucfirst($status),
    };
}

// Ambil tanggal default jika kosong
if (empty($startDate) || empty($endDate)) {
    $tanggalQuery = $koneksi->query("
        SELECT MIN(order_date) as awal, MAX(order_date) as akhir
        FROM orders
        WHERE id IN (SELECT order_id FROM transactions WHERE deleted_at IS NULL)
    ");
    $tanggalResult = $tanggalQuery->fetch(PDO::FETCH_ASSOC);
    $startDate = $tanggalResult['awal'];
    $endDate = $tanggalResult['akhir'];
}



// Query
$params = [];
$sql = "SELECT 
    o.order_date,
    v.id AS vehicle_code,
    b.name AS brand_name, vm.name AS model_name,
    v.lowest_price, v.price_displayed,
    t.grand_total, t.payment_type, t.status
    FROM transactions t
    JOIN orders o ON t.order_id = o.id
    JOIN vehicles v ON o.vehicle_id = v.id
    JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
    JOIN brands b ON vm.brand_id = b.id
    WHERE t.deleted_at IS NULL AND NOT t.status = 'cancelled'";

if ($startDate && $endDate) {
    $sql .= " AND DATE(o.order_date) BETWEEN :start AND :end";
    $params['start'] = $startDate;
    $params['end'] = $endDate;
}

$sql .= " ORDER BY o.order_date ASC";
$query = $koneksi->prepare($sql);
$query->execute($params);
$data = $query->fetchAll(PDO::FETCH_ASSOC);

// HTML
$judul = "<h2 style='text-align:center;'>Laporan Penjual Akbar Veloz</h2>";
$tanggalInfo = "<p style='text-align:center;'>Tanggal: " . date('d M Y', strtotime($startDate)) . " sampai " . date('d M Y', strtotime($endDate)) . "</p>";

$table = "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
    <thead>
        <tr style='background-color:#A9D08E;'>
            <th>No</th>
            <th>Tanggal Pesanan</th>
            <th>Kode Kendaraan</th>
            <th>Brand - Model</th>
            <th>Harga Terendah</th>
            <th>Harga Display</th>
            <th>Grand Total</th>
            <th>Jenis Pembayaran</th>
            <th>Status</th>
            <th>Margin Profit</th>
        </tr>
    </thead>
    <tbody>";

$no = 1;
$totalLowest = 0;
$totalGrand = 0;

foreach ($data as $row) {
    $tanggal = date('l, d F Y', strtotime($row['order_date']));
    $brandModel = $row['brand_name'] . ' - ' . $row['model_name'];
    $marginRp = $row['grand_total'] - $row['lowest_price'];
    $marginPercent = $row['lowest_price'] > 0 ? round(($marginRp / $row['lowest_price']) * 100) : 0;
    $margin = formatRupiah($marginRp) . " ({$marginPercent}%)";

    $table .= "<tr>
        <td>{$no}</td>
        <td>{$tanggal}</td>
        <td>{$row['vehicle_code']}</td>
        <td>{$brandModel}</td>
        <td>" . formatRupiah($row['lowest_price']) . "</td>
        <td>" . formatRupiah($row['price_displayed']) . "</td>
        <td>" . formatRupiah($row['grand_total']) . "</td>
        <td>" . ucfirst($row['payment_type']) . "</td>
        <td>" . translateStatus($row['status']) . "</td>
        <td>{$margin}</td>
    </tr>";

    $totalLowest += $row['lowest_price'];
    $totalGrand += $row['grand_total'];
    $no++;
}

$totalMarginRp = $totalGrand - $totalLowest;
$totalMarginPercent = $totalLowest > 0 ? round(($totalMarginRp / $totalLowest) * 100) : 0;
$totalMargin = formatRupiah($totalMarginRp) . " ({$totalMarginPercent}%)";

$table .= "<tr style='font-weight:bold;background-color:#f2f2f2;'>
    <td colspan='4' style='text-align:right;'>TOTAL</td>
    <td>" . formatRupiah($totalLowest) . "</td>
    <td></td>
    <td>" . formatRupiah($totalGrand) . "</td>
    <td colspan='2'></td>
    <td>{$totalMargin}</td>
</tr>";

$table .= "</tbody></table>";

$tandaTangan = "<div style='margin-top:50px; text-align:right;'>
    <p>Subang, " . date('d F Y') . "</p>
    <p>Mengetahui</p>
    <br><br><br>
    <p>(..............................)</p>
</div>";

$html = $judul . $tanggalInfo . $table . $tandaTangan;

$mpdf = new Mpdf(['format' => 'A4-L']);
$mpdf->WriteHTML($html);
$mpdf->Output('laporan_transaksi.pdf', 'I');
