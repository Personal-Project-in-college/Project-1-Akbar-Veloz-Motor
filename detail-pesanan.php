<?php

$pesanan = [
    'customer_name' => 'Givi Boy',
    'customer_email' => 'faradfsfdsfsdf@gmail.com',
    'customer_whatsapp' => '6281234567890',
    'customer_address' => 'Jl. Merdeka No. 45, Bandung, Jawa Barat, 40111',
    'purpose' => 'Test Drive',
    'schedule_date' => '2025-06-18',
    'arrival_method' => 'to_location',
    'vehicle_name' => 'K0101F - Yamaha Aerox',
    'vehicle_type' => 'Motor',
    'vehicle_color' => 'Prestige Silver',
    'vehicle_year' => '2023',
    'stnk_deadline' => '2025-07-08',
    'fuel_type' => 'Bensin',
    'engine_cc' => '155cc',
    'description' => 'Yamaha Aerox 155 Connected, skutik sporty dengan performa maksimal dan fitur canggih. Dilengkapi dengan Y-Connect untuk sinkronisasi dengan smartphone Anda.',
    'price' => 'Rp 27.425.000'
];

$today = new DateTime();
$stnkDate = new DateTime($pesanan['stnk_deadline']);
$interval = $today->diff($stnkDate);
$sisaHariSTNK = $interval->days;

$jadwalFormatted = date('d F Y', strtotime($pesanan['schedule_date']));

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
                            <dd><?php echo htmlspecialchars($pesanan['customer_whatsapp']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Alamat</dt>
                            <dd><?php echo htmlspecialchars($pesanan['customer_address']); ?></dd>
                        </div>
                    </div>
                    <div>
                        <div class="detail-item">
                            <dt>Tujuan</dt>
                            <dd><?php echo htmlspecialchars($pesanan['purpose']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Jadwal</dt>
                            <dd><?php echo htmlspecialchars($jadwalFormatted); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Metode Kedatangan</dt>
                            <dd><?php echo $pesanan['arrival_method'] == 'to_location' ? 'Petugas datang ke lokasi saya' : 'Saya akan datang ke showroom'; ?></dd>
                        </div>
                    </div>
                </dl>
                <hr>
                <dl class="detail-grid">
                    <div>
                        <div class="detail-item">
                            <dt>Nama Kendaraan</dt>
                            <dd><?php echo htmlspecialchars($pesanan['vehicle_name']); ?></dd>
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
                            <dd><?php echo htmlspecialchars($pesanan['engine_cc']); ?></dd>
                        </div>
                        <div class="detail-item">
                            <dt>Harga Kendaraan</dt>
                            <dd><?php echo htmlspecialchars($pesanan['price']); ?></dd>
                        </div>
                    </div>
                </dl>
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <dt>Deskripsi Kendaraan</dt>
                    <dd style="font-weight: 500; font-size: 0.95rem;"><?php echo htmlspecialchars($pesanan['description']); ?></dd>
                </div>
                <div class="form-actions">
                    <a href="index.html" class="btn-secondary">Kembali</a>
                    <button type="button" class="btn" onclick="window.location.href='datang-ke-showroom.php'">
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