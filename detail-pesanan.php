<?php
require 'config/koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID pesanan tidak ditemukan.";
    exit();
}

$order_id = $_GET['id'];

try {
    $stmt = $koneksi->prepare("
        SELECT
            o.id AS order_id,
            o.type_order,
            o.type_arrival,
            o.order_date,
            o.negotiated_price,
            c.name AS customer_name,
            c.email AS customer_email,
            c.phone AS customer_phone,
            c.address AS customer_address,
            v.id AS vehicle_code,
            v.type_vehicle AS vehicle_type,
            v.color AS vehicle_color,
            v.production_year AS vehicle_year,
            v.type_fuel AS fuel_type,
            v.cc_engine AS cc,
            v.price_displayed,
            v.stnk_deadline,
            v.description,
            vm.name AS model_name,
            b.name AS brand_name
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        JOIN vehicles v ON o.vehicle_id = v.id
        JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
        JOIN brands b ON vm.brand_id = b.id
        LEFT JOIN test_drivers td ON td.order_id = o.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pesanan) {
        echo "Data pesanan tidak ditemukan.";
        exit();
    }

    $pesanan['vehicle_name'] = $pesanan['brand_name'] . ' ' . $pesanan['model_name'];

    $today = new DateTime();
    $stnkDate = new DateTime($pesanan['stnk_deadline']);
    $interval = $today->diff($stnkDate);
    $sisaHariSTNK = $interval->days;

    $pesanan['stnk_remaining_days'] = $sisaHariSTNK;
    $pesanan['order_date'] = date('d F Y', strtotime($pesanan['order_date'])) ?? '-';
} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit();
}



?>
<!DOCTYPE html>
<html lang="id" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/detail-pesanan.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">Detail Pesanan Anda</div>
            <div class="card-body">
                <dl class="detail-grid">
                    <div>
                        <div class="detail-item">
                            <dt>Nama</dt>
                            <dd><?php echo htmlspecialchars($pesanan['customer_name']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Email</dt>
                            <dd><?php echo htmlspecialchars($pesanan['customer_email']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Whatsapp</dt>
                            <dd><?php echo htmlspecialchars($pesanan['customer_phone'] ?? '-'); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Alamat</dt>
                            <dd><?php echo htmlspecialchars($pesanan['customer_address'] ?? '-'); ?></dd>
                        </div>
                    </div>
                    <div>
                        <div class="detail-item">
                            <dt>Tujuan</dt>
                            <dd><?php echo htmlspecialchars($pesanan['type_order']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Jadwal</dt>
                            <dd><?php echo htmlspecialchars($pesanan['order_date']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Metode Kedatangan</dt>
                            <dd><?php echo $pesanan['type_arrival'] == 'home_visit' ? 'Petugas datang ke lokasi saya' : 'Saya akan datang ke showroom'; ?></dd>
                        </div>
                    </div>
                </dl>
                <hr>
                <dl class="detail-grid">
                    <div>
                        <div class="detail-item">
                            <dt>Nama Kendaraan</dt>
                            <dd><?php echo htmlspecialchars($pesanan['vehicle_code']); ?> - <?php echo htmlspecialchars($pesanan['vehicle_name']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Tipe</dt>
                            <dd><?php echo htmlspecialchars($pesanan['vehicle_type']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Warna</dt>
                            <dd><?php echo htmlspecialchars($pesanan['vehicle_color']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Tahun Produksi</dt>
                            <dd><?php echo htmlspecialchars($pesanan['vehicle_year']); ?></dd>
                        </div>
                    </div>
                    <div>
                        <div class="detail-item">
                            <dt>STNK Berlaku Sampai</dt>
                            <dd>
                                <?php echo date('d F Y', strtotime($pesanan['stnk_deadline'])); ?>
                                <span class="stnk-remaining">(<?php echo $sisaHariSTNK; ?> hari tersisa)</span>
                            </dd>
                        </div>
                        <div class="detail-item">
                            <dt>Bahan Bakar</dt>
                            <dd><?php echo htmlspecialchars($pesanan['fuel_type']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Kapasitas Mesin</dt>
                            <dd><?php echo htmlspecialchars($pesanan['cc']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Harga Kendaraan</dt>
                            <dd>Rp <?php echo number_format($pesanan['price_displayed'], 0, ',', '.'); ?></dd>
                        </div>
                        <?php if (!$pesanan['negotiated_price'] == 0) : ?>
                            <div class="detail-item">
                                <dt>Harga Negoisasi</dt>
                                <dd>Rp <?php echo number_format($pesanan['negotiated_price'], 0, ',', '.'); ?></dd>
                            </div>
                        <?php endif ?>
                    </div>
                </dl>
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <dt>Deskripsi Kendaraan</dt>
                    <dd style="font-weight: 500; font-size: 0.95rem;"><?php echo htmlspecialchars($pesanan['description']); ?></dd>
                </div>
                <div class="form-actions">
                    <a href="contact-us.php" class="btn-secondary">Kembali</a>
                    <button type="button" class="btn" onclick="window.location.href='tunggu-petugas.php'">
                        Kirim
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M20 4L3 9.31372L10.5 13.5M20 4L14.5 21L10.5 13.5M20 4L10.5 13.5" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </g>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>
</body>

</html>