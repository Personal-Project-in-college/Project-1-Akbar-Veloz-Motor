<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['danger_message'] = "<strong>Error:</strong> ID pesanan tidak ditemukan.";
    header("Location: ../orders/orders.php");
    exit;
}

$query = $koneksi->prepare("
    SELECT 
        transaction.*, 
        o.type_order,
        c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address,
        v.id AS vehicle_id, vm.name AS vehicle_model_name
    FROM transactions transaction
    JOIN orders o ON transaction.order_id = o.id
    JOIN customers c ON o.customer_id = c.id
    JOIN vehicles v ON o.vehicle_id = v.id
    JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
    WHERE transaction.order_id = ? AND transaction.deleted_at IS NULL
");
$query->execute([$id]);
$test_driver = $query->fetch(PDO::FETCH_ASSOC);

if (!$test_driver) {
    $_SESSION['danger_message'] = "<strong>Error:</strong> Data tidak ditemukan atau sudah dihapus.";
    header("Location: ../orders/orders.php");
    exit;
}

$vehicleQuery = $koneksi->prepare("SELECT v.*, vm.name as model_name FROM vehicles v JOIN vehicle_models vm ON v.vehicle_model_id = vm.id WHERE v.id = ?");
$vehicleQuery->execute([$test_driver['vehicle_id']]);
$vehicle = $vehicleQuery->fetch(PDO::FETCH_ASSOC);

$stnk_deadline = new DateTime($vehicle['stnk_deadline']);
$today = new DateTime();
$interval = $today->diff($stnk_deadline);
$sisaHari = $interval->days . " hari (" . ($stnk_deadline > $today ? "tersisa" : "lewat") . ")";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status']);
    $result_note = trim($_POST['result_note']);

    $updateQuery = $koneksi->prepare("UPDATE test_drivers SET status = ?, result_note = ?, updated_at = NOW() WHERE id = ?");
    $updateQuery->execute([$status, $result_note, $test_driver['id']]);

    if ($status === 'cancelled' || $status === 'finish') {
        $updateOrder = $koneksi->prepare("UPDATE orders SET status = 'finished', updated_at = NOW() WHERE id = ?");
        $updateOrder->execute([$test_driver['order_id']]);
    }

    $_SESSION['success_message'] = "Pesanan <strong>{$test_driver['customer_name']}</strong> telah diselesaikan.";
    header("Location: ../orders/orders.php");
    exit;
}

$banks = [
    ["name" => "Bank BCA", "number" => "1234567890"],
    ["name" => "Bank Mandiri", "number" => "9876543210"],
    ["name" => "Bank BRI", "number" => "1122334455"]
];

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Pesanan | Transaksi</h3>

        <!-- TABEL CUSTOMER -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Informasi Customer</h5>
                <table class="table">
                    <tr>
                        <th>Nama</th>
                        <td><?= htmlspecialchars($test_driver['customer_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($test_driver['customer_email']) ?></td>
                    </tr>
                    <tr>
                        <th>Nomor HP</th>
                        <td><?= htmlspecialchars($test_driver['customer_phone']) ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td class="text-wrap"><?= htmlspecialchars($test_driver['customer_address']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TABEL KENDARAAN -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Informasi Kendaraan</h5>
                <table class="table">
                    <tr>
                        <th>Kode</th>
                        <td>
                            <a href="../vehicles/detail.php?id=<?= $vehicle['id'] ?>" target="_blank">
                                <?= htmlspecialchars($vehicle['id']) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Tipe</th>
                        <td><?= htmlspecialchars($vehicle['type_vehicle']) ?></td>
                    </tr>
                    <tr>
                        <th>Warna</th>
                        <td><?= htmlspecialchars($vehicle['color']) ?></td>
                    </tr>
                    <tr>
                        <th>Tahun Produksi</th>
                        <td><?= htmlspecialchars($vehicle['production_year']) ?></td>
                    </tr>
                    <tr>
                        <th>STNK Deadline</th>
                        <td><?= htmlspecialchars($vehicle['stnk_deadline']) ?> <br><small class="text-muted"><?= $sisaHari ?></small></td>
                    </tr>
                    <tr>
                        <th>Bahan Bakar</th>
                        <td><?= htmlspecialchars($vehicle['type_fuel']) ?></td>
                    </tr>
                    <tr>
                        <th>CC Engine</th>
                        <td><?= htmlspecialchars($vehicle['cc_engine']) ?> cc</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td class="text-wrap"><?= htmlspecialchars($vehicle['description']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TABEL TRANSAKSI -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Transaksi</h5>
                <table class="table" id="transaction_table">
                    <tr>
                        <th>Harga Kendaraan</th>
                        <td>
                            <input type="text" id="vehicle_price" class="form-control format-rupiah" readonly value="<?= number_format($test_driver['vehicle_price'], 2, ',', '.') ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Deal Negosiasi</th>
                        <td>
                            <input type="number" id="deal_negotiation" name="deal_negotiation" class="form-control" value="<?= $test_driver['deal_negotiation'] ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        <td>
                            <input type="text" id="grand_total_display" class="form-control format-rupiah" readonly>
                            <input type="hidden" id="grand_total" name="grand_total">
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Pembayaran</th>
                        <td>
                            <select name="payment_type" id="payment_type" class="form-control" style="color: black;">
                                <option value="tunai">Tunai</option>
                                <option value="cicilan">Cicilan</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="cicilan-row" style="display:none">
                        <th>Uang Muka</th>
                        <td><input type="number" name="down_payment" class="form-control"></td>
                    </tr>
                    <tr class="cicilan-row" style="display:none">
                        <th>Jumlah Dibayarkan</th>
                        <td><input type="number" name="amount_paid" class="form-control"></td>
                    </tr>
                    <tr>
                        <th>Metode Pembayaran</th>
                        <td>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="">Pilih Metode</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="midtrans">Midtrans</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="bank_list_row" style="display:none">
                        <th>Rekening Tujuan</th>
                        <td>
                            <ul class="mb-0">
                                <?php foreach ($banks as $bank): ?>
                                    <li><?= $bank['name'] ?> - <?= $bank['number'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                    <tr id="midtrans_button_row" style="display:none">
                        <th>Aksi Midtrans</th>
                        <td><a href="#" class="btn btn-info">Bayar via Midtrans</a></td>
                    </tr>
                    <tr id="cash_transfer_button_row" style="display:none">
                        <th>Aksi Pembayaran</th>
                        <td><a href="../transactions/upload.php?order_id=<?= $test_driver['order_id'] ?>" class="btn btn-success">Upload Bukti Pembayaran</a></td>
                    </tr>

                </table>
            </div>
        </div>

        <script>
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 2
                }).format(number);
            }

            const priceInput = document.getElementById('vehicle_price');
            const negoInput = document.getElementById('deal_negotiation');
            const grandTotalHidden = document.getElementById('grand_total');
            const grandTotalDisplay = document.getElementById('grand_total_display');

            function updateGrandTotal() {
                let price = parseFloat(priceInput.value.replace(/[^\d]/g, '')) || 0;
                let nego = parseFloat(negoInput.value) || 0;
                let total = price - nego;
                grandTotalHidden.value = total.toFixed(2);
                grandTotalDisplay.value = formatRupiah(total);
            }

            document.addEventListener('DOMContentLoaded', updateGrandTotal);
            negoInput.addEventListener('input', updateGrandTotal);

            document.getElementById('payment_type').addEventListener('change', function() {
                let rows = document.querySelectorAll('.cicilan-row');
                rows.forEach(row => row.style.display = this.value === 'cicilan' ? '' : 'none');
            });

            document.getElementById('payment_method').addEventListener('change', function() {
                document.getElementById('bank_list_row').style.display = this.value === 'transfer' ? '' : 'none';
                document.getElementById('midtrans_button_row').style.display = this.value === 'midtrans' ? '' : 'none';
            });
            const cashTransferButtonRow = document.getElementById('cash_transfer_button_row');
            document.getElementById('payment_method').addEventListener('change', function() {
                const value = this.value;
                document.getElementById('bank_list_row').style.display = value === 'transfer' ? '' : 'none';
                document.getElementById('midtrans_button_row').style.display = value === 'midtrans' ? '' : 'none';
                cashTransferButtonRow.style.display = (value === 'cash' || value === 'transfer') ? '' : 'none';
            });
        </script>

    </div>
</div>

<?php include '../layout/footer.php'; ?>