<?php
session_start();
include '../../../../config/koneksi.php'; // Pastikan path ke file koneksi sudah benar

// Ambil order_id dari URL
$order_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$order_id) {
    die("Error: Order ID tidak ditemukan.");
}

try {
    $stmt = $koneksi->prepare("SELECT 
        o.id AS order_id,
        o.type_order,
        o.type_arrival,
        o.status AS order_status,
        o.order_date AS date_order,

        c.name AS customer_name,
        c.email AS customer_email,
        c.phone AS customer_phone,
        c.address AS customer_address,

        v.id AS vehicle_id,
        v.price_displayed AS vehicle_price,
        v.type_vehicle AS vehicle_type,
        v.color AS vehicle_color,
        v.production_year,
        v.stnk_deadline,
        v.type_fuel,
        v.cc_engine,

        m.name AS model_name,
        b.name AS brand_name,

        br.name AS branch_name,
        br.address AS branch_address,

        u.name AS user_name

        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        LEFT JOIN vehicles v ON o.vehicle_id = v.id
        LEFT JOIN vehicle_models m ON v.vehicle_model_id = m.id
        LEFT JOIN brands b ON m.brand_id = b.id
        LEFT JOIN branches br ON v.branch_id = br.id
        LEFT JOIN transactions t ON t.order_id = o.id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE o.id = ?");

    $stmt->execute([$order_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Data pesanan tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}

function translate_enum($field, $value) {
    $map = [
        'type_order' => [
            'test_driver' => 'Test Drive',
            'transaction' => 'Transaksi',
        ],
        'type_arrival' => [
            'showroom' => 'Langsung ke Showroom',
            'home_visit' => 'Kunjungan ke Rumah',
        ],
        'order_status' => [
            'cancelled' => 'Dibatalkan',
            'proced' => 'Diproses',
            'finished' => 'Selesai',
        ],
        'vehicle_type' => [
            'motorcycle' => 'Motor',
            'car' => 'Mobil',
        ],
        'type_fuel' => [
            'gasoline' => 'Bensin',
            'electric' => 'Listrik',
            'hybrid' => 'Hybrid',
        ],
    ];
    return $map[$field][$value] ?? $value;
}
function hitung_umur_kendaraan($tanggal) {
    $tahunProduksi = date('Y', strtotime($tanggal));
    $tahunSekarang = date('Y');
    return $tahunSekarang - $tahunProduksi;
}

$stnk_deadline = new DateTime($data['stnk_deadline']);
$today = new DateTime();
$interval = $today->diff($stnk_deadline);
$sisaHari = $interval->days . " hari (" . ($stnk_deadline > $today ? "tersisa" : "lewat") . ")";

include '../layout/header.php';
include '../layout/sidebar.php';
?>
<style>
    /* Import Font Google */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f2f5;
        /* Latar belakang abu-abu */
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 100vh;
    }

    .checkout-container {
        width: 100%;
        max-width: 800px;
        margin: 20px auto
    }

    .card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
    }

    .card-header {
        background-color: #4a90e2;
        /* Warna biru sebagai header */
        color: white;
        padding: 18px 25px;
        margin: 0;
        font-size: 1.5em;
        font-weight: 600;
    }

    .card-section {
        padding: 20px 25px;
        border-bottom: 1px solid #e8e8e8;
    }

    .card-section:last-child {
        border-bottom: none;
    }

    .card-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #333;
        font-size: 1.2em;
        border-bottom: 2px solid #4a90e2;
        padding-bottom: 8px;
        display: inline-block;
    }

    .card-section p {
        margin: 8px 0;
        line-height: 1.6;
        color: #555;
    }

    .card-section p strong {
        color: #333;
        min-width: 150px;
        display: inline-block;
    }

    /* Styling untuk form upload */
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-group input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-submit {
        display: block;
        width: 100%;
        padding: 12px;
        background-color: #28a745;
        /* Warna hijau untuk tombol submit */
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1em;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-submit:hover {
        background-color: #218838;
    }

    /* Badge untuk status */
    .status-badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.9em;
        font-weight: 500;
        color: white;
    }

    .status-badge.pending {
        background-color: #ffc107;
    }

    .status-badge.paid {
        background-color: #28a745;
    }

    .status-badge.cancelled {
        background-color: #dc3545;
    }

    .status-badge.dp {
        background-color: #17a2b8;
    }


    /* Alert message */
    .alert {
        padding: 15px;
        margin: 0 25px 15px 25px;
        border-radius: 6px;
        color: white;
    }

    .alert.success {
        background-color: #28a745;
    }



    .wrap-btn {
        display: flex;
        flex-direction: row;
        justify-content: end;
        gap: 6px;
    }
</style>

<body>

    <div class="checkout-container">
        <div class="card">
            <h2 class="card-header">Data Pesanan</h2>
            <div class="card-section">
                <h3>Detail Pesanan</h3>
                <p><strong>Tanggal Pesanan:</strong> <?php echo htmlspecialchars($data['date_order']); ?> WIB</p>
                <p><strong>Dilayani Oleh</strong> <?php echo htmlspecialchars($data['user_name'] ?? 'Belum Dilayani'); ?></p>
                <p><strong>Tipe Pesanan</strong> <?= translate_enum('type_order', $data['type_order']) ?></p>
                <p><strong>Tipe Kedatangan</strong> <?= translate_enum('type_arrival', $data['type_arrival']) ?></p>
                <p><strong>Status</strong> <?= translate_enum('order_status', $data['order_status']) ?></p>
            </div>

            <div class="card-section">
                <h3>Detail Pelanggan</h3>
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($data['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($data['customer_email']); ?></p>
                <p><strong>Telepon:</strong> <?php echo htmlspecialchars($data['customer_phone'] ?? '-'); ?></p>
                <p class="text-wrap"><strong>Alamat:</strong> <?php echo htmlspecialchars($data['customer_address'] ?? '-'); ?></p>
            </div>

            <div class="card-section">
                <h3>Detail Kendaraan</h3>
                <p><strong>ID Kendaraan:</strong> <?php echo htmlspecialchars($data['vehicle_id']); ?></p>
                <p><strong>Harga Kendaraan:</strong> Rp <?= number_format($data['vehicle_price'] ?? 0, 0, ',', '.') ?></p>
                <p><strong>Tipe:</strong> <?= translate_enum('vehicle_type', $data['vehicle_type']) ?></p>
                <p><strong>Warna:</strong> <?php echo htmlspecialchars($data['vehicle_color']); ?></p>
                <p><strong>Tahun Produksi:</strong> <?php echo htmlspecialchars(date('d F Y', strtotime($data['production_year']))); ?> | <small><?= hitung_umur_kendaraan($data['production_year']) ?> Tahun Lalu</small></p>
                <p><strong>Pajak STNK:</strong> <?php echo htmlspecialchars(date('d F Y', strtotime($data['stnk_deadline']))); ?> | <small class="text-muted"><?= $sisaHari ?></small></p>
                <p><strong>Bahan Bakar:</strong>  <?= translate_enum('type_fuel', $data['type_fuel']) ?></p>
                <p><strong>CC Mesin:</strong> <?php echo htmlspecialchars($data['cc_engine']); ?> CC</p>
            </div>

            <div class="card-section">
                <h3>Posisi Kendaraan</h3>
                <p><strong>Cabang :</strong> <?php echo htmlspecialchars($data['branch_name']); ?></p>
                <p><strong>Alamat :</strong> <?php echo htmlspecialchars($data['branch_address']); ?></p>
            </div>


            <!-- <div class="card-section">
                <h3>Aksi Lanjutan</h3>
                <div class="wrap-btn">
                    <button class="btn btn-primary" onclick="alert('Fitur kirim email belum diimplementasi')">Kirim Email</button>
                </div>
            </div> -->

        </div>
    </div>
    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../assets/vendors/chart.js/chart.umd.js"></script>
    <script src="../assets/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
    <script src="../assets/js/dataTables.select.min.js"></script>

    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/template.js"></script>
    <script src="../assets/js/settings.js"></script>
    <script src="../assets/js/todolist.js"></script>
    <script src="../assets/js/jquery.cookie.js" type="text/javascript"></script>

    <script src="../assets/js/dashboard.js"></script>
    <script src="../assets/js/table_controls.js"></script>
</body>