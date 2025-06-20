<?php
require 'config/koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['pending_order'])) {
    echo "Data pesanan belum tersedia.";
    exit();
}

$data = $_SESSION['pending_order'];
$customer_id = $_SESSION['customer_id'];

if (!isset($_SESSION['pending_order'])) {
    echo "Data pesanan belum tersedia.";
    exit();
}

$data = $_SESSION['pending_order'];
$vehicle_id = $data['vehicle_id'];
$type_arrival = $data['type_arrival'];

// Query kendaraan & relasi
$stmt = $koneksi->prepare("SELECT v.type_vehicle, v.color, v.production_year, v.type_fuel, v.cc_engine, v.price_displayed, v.stnk_deadline, v.description, vm.name AS model_name, b.name AS brand_name FROM vehicles v JOIN vehicle_models vm ON v.vehicle_model_id = vm.id JOIN brands b ON vm.brand_id = b.id WHERE v.id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

// Gabungkan ke $data
$data = array_merge($data, $vehicle);
$data['vehicle_name'] = $vehicle['brand_name'] . ' ' . $vehicle['model_name'];

$negotiated_price = 0;

// Hitung sisa hari STNK
$today = new DateTime();
$stnkDate = new DateTime($data['stnk_deadline']);
$sisaHariSTNK = $today->diff($stnkDate)->days;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['pending_order'])) {
    $data = $_SESSION['pending_order'];

    try {
        $koneksi->beginTransaction();

        // Update customer
        $stmt = $koneksi->prepare("UPDATE customers SET phone = ?, address = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$data['phone'], $data['address'], $customer_id]);

        // Insert order
        $stmtOrder = $koneksi->prepare("INSERT INTO orders (customer_id, vehicle_id, type_order, type_arrival, order_date, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmtOrder->execute([$customer_id, $data['vehicle_id'], $data['type_order'], $data['type_arrival'], $data['order_date']]);
        $order_id = $koneksi->lastInsertId();

        if ($data['type_order'] === 'test_driver') {
            $koneksi->prepare("INSERT INTO test_drivers (order_id, status, created_at) VALUES (?, 'process', NOW())")
                ->execute([$order_id]);

            $koneksi->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?")
                ->execute([$data['vehicle_id']]);
        } elseif ($data['type_order'] === 'transaction') {
            $stmt = $koneksi->prepare("SELECT price_displayed FROM vehicles WHERE id = ?");
            $stmt->execute([$data['vehicle_id']]);
            $vehicle = $stmt->fetch();

            $price = $vehicle['price_displayed'] ?? 0;
            $koneksi->prepare("INSERT INTO transactions (order_id, vehicle_price, deal_negotiation, status, created_at) VALUES (?, ?, 0, 'pending', NOW())")
                ->execute([$order_id, $price]);

            $koneksi->prepare("UPDATE vehicles SET status = 'transaction' WHERE id = ?")
                ->execute([$data['vehicle_id']]);
        }

        $koneksi->commit();
        unset($_SESSION['pending_order']); // bersihkan session

        if ($type_arrival == 'showroom') {
            header("Location: datang-ke-showroom.php?id=$order_id");
            exit();
        } else {
            header("Location: tunggu-petugas.php?id=$order_id");
            exit();
        }
    } catch (PDOException $e) {
        $koneksi->rollBack();
        echo "Gagal: " . $e->getMessage();
    }
}

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
    <title>Detail Pesanan - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/detail-pesanan.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">Detail Pesanan Anda</div>
            <form method="POST">
                <div class="card-body">
                    <dl class="detail-grid">
                        <div>
                            <div class="detail-item">
                                <dt>Nama</dt>
                                <dd><?php echo htmlspecialchars($data['name']); ?></dd>
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
                                <dd><?php echo $data['type_order'] == 'test_driver' ? 'Uji Coba Kendaraan' : 'Transaksi Kendaraan'; ?></dd>
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
                                    <span class="stnk-remaining">(<?php echo $sisaHariSTNK; ?> hari tersisa)</span>
                                </dd>
                            </div>
                            <div class="detail-item">
                                <dt>Bahan Bakar</dt>
                                <dd><?= htmlspecialchars(translate_fuel($data['type_fuel'])) ?></dd>
                            </div>

                            <div class="detail-item">
                                <dt>Kapasitas Mesin</dt>
                                <dd><?php echo htmlspecialchars($data['cc_engine']); ?> cc</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Harga Kendaraan</dt>
                                <dd>Rp <?php echo number_format($data['price_displayed'], 0, ',', '.'); ?></dd>
                            </div>
                            <?php if (!$negotiated_price == 0) : ?>
                                <div class="detail-item">
                                    <dt>Harga Negoisasi</dt>
                                    <dd>Rp <?php echo number_format($negotiated_price, 0, ',', '.'); ?></dd>
                                </div>
                            <?php endif ?>
                        </div>
                    </dl>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <dt>Deskripsi Kendaraan</dt>
                        <dd style="font-weight: 500; font-size: 0.95rem;"><?php echo htmlspecialchars($data['description']); ?></dd>
                    </div>
                    <div class="form-actions">
                        <a href="contact-us.php" class="btn-secondary">Kembali</a>
                        <button type="submit" class="btn">
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
            </form>
        </div>
    </div>
</body>

</html>