<?php
session_start();
include '../../../../config/koneksi.php'; // Pastikan path ke file koneksi sudah benar

// Ambil order_id dari URL
$order_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$order_id) {
    die("Error: Order ID tidak ditemukan.");
}

try {
    // Query baru sesuai dengan struktur relasi yang benar
    $stmt = $koneksi->prepare("SELECT t.order_id, t.vehicle_price, t.deal_negotiation, t.grand_total, t.payment_type, t.down_payment, t.remaining_amount, t.payment_method, t.status, t.payment_gateway_ref, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address, v.id AS vehicle_id, v.type_vehicle AS vehicle_type, v.color AS vehicle_color, v.production_year, v.stnk_deadline, v.type_fuel, v.cc_engine, u.name AS user_name FROM transactions t LEFT JOIN orders o ON t.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id LEFT JOIN vehicles v ON o.vehicle_id = v.id LEFT JOIN users u ON t.user_id = u.id WHERE t.order_id = ?");
    $stmt->execute([$order_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Data transaksi tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
include '../layout/header.php';
include '../layout/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Transaksi - <?php echo htmlspecialchars($data['order_id']); ?></title>
</head>

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
            <h2 class="card-header">Detail Checkout</h2>

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
                <p><strong>Tipe:</strong> <?php echo htmlspecialchars($data['vehicle_type']); ?></p>
                <p><strong>Warna:</strong> <?php echo htmlspecialchars($data['vehicle_color']); ?></p>
                <p><strong>Tahun Produksi:</strong> <?php echo htmlspecialchars($data['production_year']); ?></p>
                <p><strong>Pajak STNK:</strong> <?php echo htmlspecialchars(date('d F Y', strtotime($data['stnk_deadline']))); ?></p>
                <p><strong>Bahan Bakar:</strong> <?php echo htmlspecialchars($data['type_fuel']); ?></p>
                <p><strong>CC Mesin:</strong> <?php echo htmlspecialchars($data['cc_engine']); ?> CC</p>
            </div>

            <div class="card-section">
                <h3>Detail Transaksi</h3>
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($data['order_id']); ?></p>
                <p><strong>Penjual:</strong> <?php echo htmlspecialchars($data['user_name']); ?></p>
                <p><strong>Harga Kendaraan:</strong> Rp <?php echo number_format($data['vehicle_price'], 0, ',', '.'); ?></p>
                <p><strong>Deal Negosiasi:</strong> Rp <?= number_format($data['deal_negotiation'] ?? 0, 0, ',', '.') ?></p>
                <p><strong>Total Bayar:</strong> Rp <?php echo number_format($data['grand_total'], 0, ',', '.'); ?></p>
                <p><strong>Tipe Pembayaran:</strong> <?php echo htmlspecialchars(ucfirst($data['payment_type'])); ?></p>

                <?php if (!is_null($data['down_payment']) && $data['down_payment'] > 0): ?>
                    <p><strong>Uang Muka (DP):</strong> Rp <?php echo number_format($data['down_payment'], 0, ',', '.'); ?></p>
                    <p><strong>Sisa Pembayaran:</strong> Rp <?php echo number_format($data['remaining_amount'], 0, ',', '.'); ?></p>
                <?php endif; ?>

                <?php if ($data['down_payment'] > 0): ?>
                    <p><strong>Sudah Dibayar:</strong> Rp <?php echo number_format($data['down_payment'], 0, ',', '.'); ?></p>
                <?php endif; ?>

                <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($data['payment_method']); ?></p>
                <p><strong>Status:</strong> <span class="status-badge <?php echo strtolower($data['status']); ?>"><?php echo htmlspecialchars(ucfirst($data['status'])); ?></span></p>

                <?php if (!is_null($data['payment_gateway_ref'])): ?>
                    <p><strong>Referensi Gateway:</strong> <?php echo htmlspecialchars($data['payment_gateway_ref']); ?></p>
                <?php endif; ?>
            </div>

            <div class="card-section">
                <h3>Aksi Lanjutan</h3>
                <div class="wrap-btn">
                    <button class="btn btn-dark" onclick="window.print()">Cetak Struk</button>
                    <button class="btn btn-primary" onclick="alert('Fitur kirim email belum diimplementasi')">Kirim Email</button>
                </div>

            </div>

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

</html>