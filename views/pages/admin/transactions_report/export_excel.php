<?php
require '../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include '../../../../config/koneksi.php';

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

// Helper: Format rupiah
function formatRupiah($number)
{
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Helper: Translate status
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

// Ambil data
$params = [];
$sql = "SELECT 
    o.order_date,
    u.name AS user_name,
    c.name AS customer_name, c.email, c.phone, c.address,
    v.id AS vehicle_code, b.name AS brand_name, vm.name AS model_name,
    v.lowest_price, v.price_displayed AS display_price,
    t.deal_negotiation, t.grand_total, t.payment_type, t.payment_method, t.status, t.user_id
    FROM transactions t
    JOIN orders o ON t.order_id = o.id
    JOIN users u ON t.user_id = u.id
    JOIN customers c ON o.customer_id = c.id
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

// Siapkan data untuk tiap sheet
$sheets = [
    'All Data' => $data,
    'Tunai' => array_filter($data, fn($row) => $row['payment_type'] === 'tunai'),
    'Cicilan' => array_filter($data, fn($row) => $row['payment_type'] === 'cicilan'),
];


$spreadsheet = new Spreadsheet();

$styleHeader = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A9D08E']]
];

$spreadsheet->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleHeader);

$sheetIndex = 0;
foreach ($sheets as $sheetName => $rows) {
    $sheet = $sheetIndex === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
    $sheet->setTitle($sheetName);

    // Header
    $headers = ['No', 'Tanggal Pesanan', 'Nama Karyawan', 'Nama Customer', 'Email', 'Phone', 'Alamat', 'Kode Kendaraan', 'Brand - Model', 'Harga Terendah', 'Harga Display', 'Deal Negosiasi', 'Grand Total', 'Jenis Pembayaran', 'Metode Pembayaran', 'Status', 'Margin Profit'];
    $sheet->fromArray($headers, null, 'A1');
    $sheet->getStyle('A1:Q1')->applyFromArray($styleHeader); // 🟢 Terapkan style ke semua sheet

    $no = 1;
    $rowNum = 2;
    foreach ($rows as $row) {
        $tanggal = date('l, d F Y', strtotime($row['order_date']));
        $brandModel = $row['brand_name'] . ' - ' . $row['model_name'];

        $lowest = (int) $row['lowest_price'];
        $total = (int) $row['grand_total'];
        $marginRp = $total - $lowest;
        $marginPercent = $lowest > 0 ? round(($marginRp / $lowest) * 100) : 0;
        $margin = formatRupiah($marginRp) . " ({$marginPercent}%)";

        $sheet->fromArray([
            $no++,
            $tanggal,
            $row['user_name'],
            $row['customer_name'],
            $row['email'],
            $row['phone'],
            $row['address'],
            $row['vehicle_code'],
            $brandModel,
            formatRupiah($lowest),
            formatRupiah($row['display_price']),
            formatRupiah($row['deal_negotiation']),
            formatRupiah($total),
            ucfirst($row['payment_type']),
            ucfirst($row['payment_method']),
            translateStatus($row['status']),
            $margin
        ], null, 'A' . $rowNum++);
    }

    // 🟢 AutoSize + Lebar kolom Alamat
    foreach (range('A', 'Q') as $col) {
        if ($col === 'G') {
            $sheet->getColumnDimension($col)->setWidth(18);
        } else {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    $sheetIndex++;
}

// 🟢 Atur sheet aktif ke All Data
$spreadsheet->setActiveSheetIndex(0);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="export_transaksi.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
