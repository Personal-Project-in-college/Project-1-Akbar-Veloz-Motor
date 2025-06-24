<?php
require 'config/koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    echo "ID pesanan tidak ditemukan.";
    exit();
}

// Ambil order beserta info kendaraan dan customer
$stmt = $koneksi->prepare("
    SELECT o.*, c.name AS customer_name, c.phone, c.email, c.address,
           v.type_vehicle, v.color, v.production_year, v.type_fuel, v.cc_engine, v.price_displayed, v.stnk_deadline, v.description,
           vm.name AS model_name, b.name AS brand_name,
           t.id AS transaction_id, t.deal_negotiation, t.grand_total, t.payment_type, t.down_payment, t.remaining_amount, t.payment_method
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    JOIN vehicles v ON o.vehicle_id = v.id
    JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
    JOIN brands b ON vm.brand_id = b.id
    LEFT JOIN transactions t ON t.order_id = o.id
    WHERE t.id = ?
");
$stmt->execute([$order_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Data pesanan tidak ditemukan.";
    exit();
}

$data['vehicle_name'] = $data['brand_name'] . ' ' . $data['model_name'];

// Hitung sisa hari STNK
$today = new DateTime();
$stnkDate = new DateTime($data['stnk_deadline']);
$sisaHariSTNK = $today->diff($stnkDate)->days;

function hitung_umur_kendaraan($tanggal)
{
    $tahunProduksi = date('Y', strtotime($tanggal));
    $tahunSekarang = date('Y');
    return $tahunSekarang - $tahunProduksi;
}

function translate_fuel($value)
{
    $map = [
        'gasoline' => 'Bensin',
        'electric' => 'Listrik',
        'hybrid' => 'Hybrid'
    ];
    return $map[$value] ?? $value;
}

?>
<!DOCTYPE html>
<html lang="id" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/detail-pesanan.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">Detail Transaksi Anda</div>
            <form method="POST">
                <div class="card-body">
                    <dl class="detail-grid">
                        <div>
                            <div class="detail-item">
                                <dt>Nama</dt>
                                <dd><?php echo htmlspecialchars($data['customer_name']); ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Email</dt>
                                <dd><?php echo htmlspecialchars($data['email']); ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Whatsapp</dt>
                                <dd><?php echo htmlspecialchars($data['phone'] ?? '-'); ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Alamat</dt>
                                <dd><?php echo htmlspecialchars($data['address'] ?? '-'); ?></dd>
                            </div>
                        </div>
                        <div>
                            <div class="detail-item">
                                <dt>Tujuan</dt>
                                <dd><?php echo $data['type_order'] == 'test_drive' ? 'Uji Coba Kendaraan' : 'Transaksi Kendaraan'; ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Jadwal</dt>
                                <dd><?php echo htmlspecialchars(date('d F Y H.i', strtotime($data['order_date']))) . ' WIB'; ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Metode Kedatangan</dt>
                                <dd><?php echo $data['type_arrival'] == 'home_visit' ? 'Petugas datang ke lokasi saya' : 'Saya akan datang ke showroom'; ?></dd>
                            </div>
                        </div>
                    </dl>
                    <hr>
                    <dl class="detail-grid">
                        <div>
                            <div class="detail-item">
                                <dt>Nama Kendaraan</dt>
                                <dd><?php echo htmlspecialchars($data['vehicle_id']); ?> - <?php echo htmlspecialchars($data['vehicle_name']); ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Tipe</dt>
                                <dd><?php echo $data['type_vehicle'] == 'motorcycle' ? 'Motor' : 'Mobil'; ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Warna</dt>
                                <dd><?php echo htmlspecialchars($data['color']); ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Tahun Produksi</dt>
                                <dd><?php echo date('d F Y', strtotime($data['production_year'])); ?>
                                    <span class="stnk-remaining" style="color: cadetblue;">(<?= hitung_umur_kendaraan($data['production_year']) ?> Tahun Lalu)</span>
                                </dd>
                            </div>
                        </div>
                        <div>
                            <div class="detail-item">
                                <dt>STNK Berlaku Sampai</dt>
                                <dd>
                                    <?php echo date('d F Y', strtotime($data['stnk_deadline'])); ?>
                                    <span class="stnk-remaining"><?php echo $sisaHariSTNK; ?> hari tersisa</span>
                                </dd>
                            </div>
                            <div class="detail-item">
                                <dt>Bahan Bakar</dt>
                                <dd><?= htmlspecialchars(translate_fuel($data['type_fuel'])) ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Harga Kendaraan</dt>
                                <dd>Rp <?php echo number_format($data['price_displayed'], 0, ',', '.'); ?></dd>
                            </div>
                            <?php if (!$data['negotiated_price'] == 0) : ?>
                                <div class="detail-item">
                                    <dt>Harga Negoisasi</dt>
                                    <dd>Rp <?php echo number_format($data['negotiated_price'], 0, ',', '.'); ?></dd>
                                </div>
                            <?php endif ?>
                        </div>
                    </dl>
                    <hr>
                    <dl class="detail-grid">
                        <div class="detail-item">
                            <dt>ID Transaksi</dt>
                            <dd>TRX-<?php echo htmlspecialchars($data['transaction_id'] ?? '-'); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Harga Deal</dt>
                            <dd>Rp <?php echo number_format($data['deal_negotiation'], 0, ',', '.'); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Total Transaksi</dt>
                            <dd>Rp <?php echo number_format($data['grand_total'], 0, ',', '.'); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Jenis Pembayaran</dt>
                            <dd><?php echo ucfirst($data['payment_type']); ?></dd>
                        </div>
                        <?php if ($data['payment_type'] === 'cicilan' && $data['down_payment'] > 0 && $data['remaining_amount'] > 0): ?>
                            <div class="detail-item">
                                <dt>Uang Muka (DP)</dt>
                                <dd>Rp <?php echo number_format($data['down_payment'], 0, ',', '.'); ?></dd>
                            </div>
                            <div class="detail-item">
                                <dt>Sisa Pembayaran</dt>
                                <dd>Rp <?php echo number_format($data['remaining_amount'], 0, ',', '.'); ?></dd>
                            </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <dt>Metode Pembayaran</dt>
                            <dd><?php echo ucfirst($data['payment_method']); ?></dd>
                        </div>
                    </dl>
                    <div class="form-actions">
                        <a href="index.php" class="btn-secondary">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
