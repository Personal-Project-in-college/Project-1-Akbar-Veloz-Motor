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

$query = $koneksi->prepare("SELECT transaction.*, o.type_order, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address, v.id AS vehicle_id, v.lowest_price AS lowest_price, vm.name AS vehicle_model_name FROM transactions transaction JOIN orders o ON transaction.order_id = o.id JOIN customers c ON o.customer_id = c.id JOIN vehicles v ON o.vehicle_id = v.id JOIN vehicle_models vm ON v.vehicle_model_id = vm.id WHERE transaction.order_id = ? AND transaction.deleted_at IS NULL");
$query->execute([$id]);
$transaction = $query->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    $_SESSION['danger_message'] = "<strong>Error:</strong> Data tidak ditemukan atau sudah dihapus.";
    header("Location: ../orders/orders.php");
    exit;
}

$vehicleQuery = $koneksi->prepare("SELECT vehicles.*, vehicle_models.name AS model_name, brands.name AS brand_name FROM vehicles JOIN vehicle_models ON vehicles.vehicle_model_id = vehicle_models.id JOIN brands ON vehicle_models.brand_id = brands.id WHERE vehicles.id = ?");
$vehicleQuery->execute([$transaction['vehicle_id']]);
$vehicle = $vehicleQuery->fetch(PDO::FETCH_ASSOC);

$stnk_deadline = new DateTime($vehicle['stnk_deadline']);
$today = new DateTime();
$interval = $today->diff($stnk_deadline);
$sisaHari = $interval->days . " hari (" . ($stnk_deadline > $today ? "tersisa" : "lewat") . ")";

// Ambil list user dengan role = 2 (misal: Petugas Test Drive)
$staffQuery = $koneksi->prepare("SELECT id, name FROM users WHERE role_id = 2 AND deleted_at IS NULL");
$staffQuery->execute();
$staffList = $staffQuery->fetchAll(PDO::FETCH_ASSOC);

// Kondisi apakah user_id sudah terisi (test driver sudah dilayani)
$alreadyAssigned = !empty($transaction['user_id']);

$currentUserId    = $_SESSION['user_id'];
$currentUserName  = $_SESSION['name'];
$currentUserPhone = $_SESSION['phone'];
$currentUserRole  = $_SESSION['role_id'];


if (isset($_POST['submit_deal'])) {
    $orderId = $_POST['order_id'];
    $deal = str_replace('.', '', $_POST['deal_negotiation']); // hapus titik
    $grandTotal = (int) $deal;

    $stmt = $koneksi->prepare("UPDATE transactions SET deal_negotiation = ?, grand_total = ? WHERE order_id = ?");
    $stmt->execute([$deal, $grandTotal, $orderId]);

    // Refresh halaman biar muncul form pembayaran
    header("Location: transaction.php?id=" . $orderId);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_user'])) {
        // Simpan petugas
        $user_id = $_POST['user_id'];
        $updateStaff = $koneksi->prepare("UPDATE transactions SET user_id = ?, updated_at = NOW() WHERE id = ?");
        $updateStaff->execute([$user_id, $transaction['id']]);
        $_SESSION['success_message'] = "Petugas telah ditugaskan.";
        header("Location: transaction.php?id=" . $orderId);
        exit;
    } else {
        $order_id = $_POST['order_id'];
        $payment_type = $_POST['payment_type'];
        $down_payment = $_POST['down_payment']; // dari input form
        $remaining_amount = $_POST['remaining_amount'];
        $payment_method = $_POST['payment_method'];

        // Tentukan status berdasarkan jenis pembayaran
        $status = ($payment_type === 'cicilan') ? 'dp_paid' : 'paid';

        try {
            // Ambil data transaksi dan slug customer
            $stmtData = $koneksi->prepare("SELECT transactions.id, customers.slug AS customer_slug, transactions.created_at 
                FROM transactions 
                JOIN orders ON orders.id = transactions.order_id 
                JOIN customers ON customers.id = orders.customer_id 
                WHERE transactions.order_id = ?");
            $stmtData->execute([$order_id]);
            $data = $stmtData->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                $_SESSION['danger_message'] = "Data transaksi tidak ditemukan.";
                header("Location: transaction.php?id=" . $order_id);
                exit;
            }

            $customerSlug = $data['customer_slug'];
            $createdAtSlug = date('Ymd_His', strtotime($data['created_at']));
            $basePath = '../../../../storage/transactions/transaction_' . $customerSlug . '/transaction_' . $createdAtSlug;

            // Buat folder jika belum ada
            if (!is_dir($basePath)) {
                mkdir($basePath, 0777, true);
            }

            // Tentukan status berdasarkan jenis pembayaran
            $status = ($payment_type === 'cicilan') ? 'dp_paid' : 'paid';

            // Ambil vehicle_id dari order yang selesai
            $getVehicleIdStmt = $koneksi->prepare("SELECT vehicle_id FROM orders WHERE id = ?");
            $getVehicleIdStmt->execute([$orderRefId]);
            $vehicleId = $getVehicleIdStmt->fetchColumn();

            // Path dan upload file tetap sama...
            // ↓ setelah selesai handle upload, bagian ini yang kita ubah
            if ($payment_type === 'cicilan') {
                if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                    $paymentProofFile = $_FILES['payment_proof'];
                    $ext = pathinfo($paymentProofFile['name'], PATHINFO_EXTENSION);
                    $filename = 'payment_proof_' . time() . '.' . $ext;
                    $fullPath = $basePath . '/' . $filename;
                    $relativePath = str_replace('../../../../', '', $fullPath);

                    move_uploaded_file($paymentProofFile['tmp_name'], $fullPath);

                    $stmt = $koneksi->prepare("UPDATE transactions SET payment_type = ?, down_payment = ?, remaining_amount = ?, payment_method = ?, payment_proof = ?, status = ?, updated_at = NOW() WHERE order_id = ?");
                    $stmt->execute([$payment_type, $down_payment, $remaining_amount, $payment_method, $relativePath, $status, $order_id]);

                    $updateOrder = $koneksi->prepare("UPDATE orders SET status = 'finished', updated_at = NOW() WHERE id = ?");
                    $updateOrder->execute([$order_id]);

                    // Update status kendaraan jadi sold
                    if ($vehicleId) {
                        $koneksi->prepare("UPDATE vehicles SET status = 'sold', updated_at = NOW() WHERE id = ?")->execute([$vehicleId]);
                    }
                } else {
                    $stmt = $koneksi->prepare("UPDATE transactions SET payment_type = ?, down_payment = ?, remaining_amount = ?, payment_method = ?, status = ?, updated_at = NOW() WHERE order_id = ?");
                    $stmt->execute([$payment_type, $down_payment, $remaining_amount, $payment_method, $status, $order_id]);

                    $updateOrder = $koneksi->prepare("UPDATE orders SET status = 'finished', updated_at = NOW() WHERE id = ?");
                    $updateOrder->execute([$order_id]);

                    // Update status kendaraan jadi sold
                    if ($vehicleId) {
                        $koneksi->prepare("UPDATE vehicles SET status = 'sold', updated_at = NOW() WHERE id = ?")->execute([$vehicleId]);
                    }
                }
            } else { // pembayaran tunai
                if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                    $paymentProofFile = $_FILES['payment_proof'];
                    $ext = pathinfo($paymentProofFile['name'], PATHINFO_EXTENSION);
                    $filename = 'payment_proof_' . time() . '.' . $ext;
                    $fullPath = $basePath . '/' . $filename;
                    $relativePath = str_replace('../../../../', '', $fullPath);

                    move_uploaded_file($paymentProofFile['tmp_name'], $fullPath);

                    $stmt = $koneksi->prepare("UPDATE transactions SET payment_type = ?, payment_method = ?, payment_proof = ?, status = ?, updated_at = NOW() WHERE order_id = ?");
                    $stmt->execute([$payment_type, $payment_method, $relativePath, $status, $order_id]);

                    $updateOrder = $koneksi->prepare("UPDATE orders SET status = 'finished', updated_at = NOW() WHERE id = ?");
                    $updateOrder->execute([$order_id]);

                    // Update status kendaraan jadi sold
                    if ($vehicleId) {
                        $koneksi->prepare("UPDATE vehicles SET status = 'sold', updated_at = NOW() WHERE id = ?")->execute([$vehicleId]);
                    }
                } else {
                    $stmt = $koneksi->prepare("UPDATE transactions SET payment_type = ?, payment_method = ?, status = ?, updated_at = NOW() WHERE order_id = ?");
                    $stmt->execute([$payment_type, $payment_method, $status, $order_id]);

                    $updateOrder = $koneksi->prepare("UPDATE orders SET status = 'finished', updated_at = NOW() WHERE id = ?");
                    $updateOrder->execute([$order_id]);

                    // Update status kendaraan jadi sold
                    if ($vehicleId) {
                        $koneksi->prepare("UPDATE vehicles SET status = 'sold', updated_at = NOW() WHERE id = ?")->execute([$vehicleId]);
                    }
                }
            }

            $_SESSION['success_message'] = "Transaksi berhasil diperbarui.";
            header("Location: transaction.php?id=" . $order_id);
            exit;
        } catch (PDOException $e) {
            $_SESSION['danger_message'] = "Gagal menyimpan transaksi: " . $e->getMessage();
            header("Location: transaction.php?id=" . $order_id);
            exit;
        }
    }
}

$filePath = $transaction['payment_proof'];

function translate_enum($field, $value) {
    $map = [
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
                        <th class="w-25">Nama</th>
                        <td><?= htmlspecialchars($transaction['customer_name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Email</th>
                        <td><?= htmlspecialchars($transaction['customer_email']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Nomor HP</th>
                        <td><?= htmlspecialchars($transaction['customer_phone'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Alamat</th>
                        <td class="text-wrap"><?= htmlspecialchars($transaction['customer_address'] ?? '-') ?></td>
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
                        <th class="w-25">Kode</th>
                        <td>
                            <a href="../vehicles/detail.php?id=<?= $vehicle['id'] ?>" target="_blank">
                                <?= htmlspecialchars($vehicle['id']) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Merek</th>
                        <td><?= htmlspecialchars($vehicle['brand_name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Model</th>
                        <td><?= htmlspecialchars($vehicle['model_name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Tipe</th>
                        <td><?= translate_enum('vehicle_type', $vehicle['type_vehicle']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Warna</th>
                        <td><?= htmlspecialchars($vehicle['color']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Tahun Produksi</th>
                        <td><?= htmlspecialchars($vehicle['production_year']) ?> <br><small class="text-muted"><?= hitung_umur_kendaraan($vehicle['production_year']) ?> Tahun lalu</small></td>
                    </tr>
                    <tr>
                        <th class="w-25">STNK Deadline</th>
                        <td><?= htmlspecialchars($vehicle['stnk_deadline']) ?> <br><small class="text-muted"><?= $sisaHari ?></small></td>
                    </tr>
                    <tr>
                        <th class="w-25">Bahan Bakar</th>
                        <td><?= translate_enum('type_fuel', $vehicle['type_fuel']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">CC Engine</th>
                        <td><?= htmlspecialchars($vehicle['cc_engine']) ?> cc</td>
                    </tr>
                    <tr>
                        <th class="w-25">Deskripsi</th>
                        <td class="text-wrap"><?= htmlspecialchars($vehicle['description']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TABEL PETUGAS -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Dilayani Oleh</h5>
                <table class="table">
                    <tr>
                        <th class="w-25">Petugas</th>
                        <td>
                            <?= htmlspecialchars($currentUserName) ?>
                            <span class="badge bg-info mx-2"><?= $currentUserRole == 1 ? 'Owner' : 'Karyawan' ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Nomor Hp</th>
                        <td><?= htmlspecialchars($currentUserPhone) ?></td>
                    </tr>
                </table>

                <?php if ($transaction['user_id'] == null): ?>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="assign_user" value="1">
                        <input type="hidden" name="user_id" value="<?= $currentUserId ?>">
                        <button type="submit" class="btn btn-primary">Saya Akan Menangani</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>



        <!-- TABEL NEGOISASI -->
        <?php if ($alreadyAssigned): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Negosiasi</h5>
                    <table class="table mb-3">
                        <tr>
                            <th class="w-25">Harga Kendaraan</th>
                            <td>Rp <?= number_format($transaction['vehicle_price'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Harga Terendah</th>
                            <td>Rp <?= number_format($transaction['lowest_price'], 0, ',', '.') ?></td>
                        </tr>
                        <?php if (!empty($transaction['deal_negotiation'])): ?>
                            <!-- ✅ Tampilkan data deal -->
                            <tr>
                                <th class="w-25">Deal Negosiasi</th>
                                <td>Rp. <?= number_format($transaction['deal_negotiation'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>

                    <!-- ✅ Tombol Edit jika deal sudah ada -->
                    <?php if (!empty($transaction['deal_negotiation']) && $transaction['status'] !== 'cancelled' && $transaction['status'] !== 'paid' && $transaction['status'] !== 'dp_paid'): ?>
                        <button id="editDealBtn" onclick="showEditDealForm()" class="btn btn-dark text-white">Edit Deal</button>
                    <?php endif; ?>


                    <!-- ✅ Form Edit (disembunyikan dulu) -->
                    <div id="editDealForm" style="display: <?= empty($transaction['deal_negotiation']) ? 'block' : 'none' ?>;">
                        <form method="POST" action="" onsubmit="return validateDealNegotiation(<?= $transaction['lowest_price'] ?>)">
                            <input type="hidden" name="order_id" value="<?= $transaction['order_id'] ?>">
                            <div class="mb-3">
                                <label>Deal Negosiasi<span class="text-danger">*</span></label>
                                <input type="number" id="dealInput" name="deal_negotiation" class="form-control" value="<?= $transaction['deal_negotiation'] ?>" required>
                            </div>
                            <button type="submit" name="submit_deal" class="btn btn-primary">
                                <?= empty($transaction['deal_negotiation']) ? 'Simpan' : 'Update' ?>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        <?php endif; ?>



        <!-- TABEL TRANSAKSI -->
        <?php if ($alreadyAssigned && $transaction['deal_negotiation'] > 0): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Transaksi</h5>

                    <?php if (in_array($transaction['status'], ['paid', 'dp_paid'])): ?>
                        <table class="table">
                            <tr>
                                <th class="w-25">Jenis Pembayaran</th>
                                <td><?= $transaction['payment_type'] ?></td>
                            </tr>

                            <?php if (!empty($transaction['down_payment'] && $transaction['remaining_amount'])): ?>
                                <!-- Jika Cicilan Tampilkan -->
                                <tr>
                                    <th class="w-25">Uang DP</th>
                                    <td>Rp <?= number_format($transaction['down_payment'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th class="w-25">Sisa Pembayaran</th>
                                    <td>Rp <?= number_format($transaction['remaining_amount'], 0, ',', '.') ?></td>
                                </tr>
                                <!-- End Cicilan Tampilkan -->
                            <?php endif; ?>

                            <tr>
                                <th class="w-25">Metode Pembayaran</th>
                                <td><?= $transaction['payment_method'] ?></td>
                            </tr>
                            <tr>
                                <th class="w-25">Status</th>
                                <td><?= $transaction['status'] ?></td>
                            </tr>


                            <!-- Tampilkan jika not null saja -->
                            <?php if (!empty($transaction['payment_gateway_ref'])): ?>
                                <tr>
                                    <th class="w-25">Midtrans Ref</th>
                                    <td><?= $transaction['payment_gateway_ref'] ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if (!empty($transaction['payment_proof'])): ?>
                                <tr>
                                    <th class="w-25">Bukti Pembayaran</th>
                                    <td><a href="../../../../<?= $filePath ?>" target="_blank">Lihat Bukti</a></td>
                                </tr>
                            <?php endif; ?>
                        </table>

                    <?php endif; ?>

                    <?php if (!in_array($transaction['status'], ['paid', 'dp_paid', 'cancelled'])): ?>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="order_id" value="<?= $transaction['order_id'] ?>">

                            <table class="table" id="transaction_table">
                                <tr>
                                    <th class="w-25">Jenis Pembayaran<span class="text-danger">*</span></th>
                                    <td>
                                        <select name="payment_type" id="payment_type" class="form-control" style="color: black;">
                                            <option value="">Pilih Jenis Pembayaran</option>
                                            <option value="tunai">Tunai</option>
                                            <option value="cicilan">Cicilan</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class="cicilan-row" style="display:none">
                                    <th class="w-25">Jenis DP<span class="text-danger">*</span></th>
                                    <td>
                                        <select id="dp_type" class="form-control" style="color: black;">
                                            <option value="">Pilih Jenis</option>
                                            <option value="persen">Persentase</option>
                                            <option value="nominal">Nominal Langsung</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="cicilan-row persen-row" style="display:none">
                                    <th class="w-25">Persentase DP (%)<span class="text-danger">*</span></th>
                                    <td>
                                        <div class="input-group">
                                            <input style="border: 0;" type="number" id="dp_percentage" class="form-control" value="0">
                                            <div class="input-group-append" style="border: 0; color:black"><span style="border: 0; color:black" class="input-group-text">%</span></div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary quick-percent">3%</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary quick-percent">5%</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary quick-percent">8%</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary quick-percent">12%</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary quick-percent">20%</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="cicilan-row nominal-row" style="display:none">
                                    <th class="w-25">Nominal DP<span class="text-danger">*</span></th>
                                    <td>
                                        <input style="border: 0;" type="number" id="dp_nominal" class="form-control" value="0">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-success quick-nominal" data-nominal="1000000">1.000.000</button>
                                            <button type="button" class="btn btn-sm btn-outline-success quick-nominal" data-nominal="2000000">2.000.000</button>
                                            <button type="button" class="btn btn-sm btn-outline-success quick-nominal" data-nominal="3000000">3.000.000</button>
                                            <button type="button" class="btn btn-sm btn-outline-success quick-nominal" data-nominal="5000000">5.000.000</button>
                                            <button type="button" class="btn btn-sm btn-outline-success quick-nominal" data-nominal="10000000">10.000.000</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="cicilan-row" style="display:none">
                                    <th class="w-25">Jumlah Dibayarkan<span class="text-danger">*</span></th>
                                    <td>
                                        <input style="border: 0;" type="number" name="down_payment" class="form-control" value="0">
                                        <input type="hidden" id="deal_negotiation_value" value="<?= $transaction['deal_negotiation'] ?>">
                                    </td>
                                </tr>
                                <tr class="cicilan-row total-row" style="display:none">
                                    <th class="w-25">Sisa Pembayaran Nanti</th>
                                    <td><input style="border: 0;" type="text" name="remaining_amount" id="remaining_amount" class="form-control" readonly value="0"></td>
                                </tr>
                                <tr style="display:none">
                                    <th class="w-25">Metode Pembayaran<span class="text-danger">*</span></th>
                                    <td>
                                        <select name="payment_method" id="payment_method" class="form-control" style="color: black;">
                                            <option value="">Pilih Metode</option>
                                            <option value="cash">Cash</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="midtrans">Midtrans</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="bank_list_row" style="display:none">
                                    <th class="w-25">Rekening Tujuan</th>
                                    <td>
                                        <ul class="mb-0">
                                            <?php foreach ($banks as $bank): ?>
                                                <li><?= $bank['name'] ?> - <?= $bank['number'] ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                                <tr id="midtrans_button_row" style="display:none">
                                    <th class="w-25">Aksi Midtrans</th>
                                    <td><a href="#" id="midtrans_btn" class="btn btn-primary text-white" data-order-id="<?= $transaction['order_id'] ?>">Bayar via Midtrans</a>
                                    </td>
                                </tr>
                                <tr id="payment_proof_button_row" style="display:none">
                                    <th class="w-25">Bukti Pembayaran<span class="text-danger">*</span></th>
                                    <td><input type="file" name="payment_proof" accept="image/*" class="form-control" required></td>
                                </tr>
                            </table>
                            <a href="cancel_transaction.php?id=<?= $transaction['id'] ?>" class="mt-3 btn btn-danger text-white">Batalkan Transaksi</a>
                            <button type="submit" class="mt-3 mx-2 btn btn-primary">Simpan</button>
                        </form>
                    <?php elseif ($transaction['status'] === 'cancelled'): ?>
                        <div>
                            <p class="text-danger">Pesanan Dibatalkan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const paymentType = document.getElementById('payment_type');
                const paymentMethod = document.getElementById('payment_method');
                const dealNegotiation = parseFloat(document.getElementById('deal_negotiation_value').value);

                const amountPaidRow = document.querySelector('input[name="down_payment"]').closest('tr');
                const amountPaidInput = document.querySelector('input[name="down_payment"]');

                const bankListRow = document.getElementById('bank_list_row');
                const midtransButtonRow = document.getElementById('midtrans_button_row');
                const paymentProofRow = document.getElementById('payment_proof_button_row');

                const dpTypeRow = document.getElementById('dp_type').closest('tr');
                const dpTypeSelect = document.getElementById('dp_type');
                const dpPercentageRow = document.querySelector('.persen-row');
                const dpNominalRow = document.querySelector('.nominal-row');
                const dpPercentageInput = document.getElementById('dp_percentage');
                const dpNominalInput = document.getElementById('dp_nominal');

                const downPaymentInput = document.querySelector('input[name="down_payment"]');
                const remainingAmountInput = document.getElementById('remaining_amount');
                const totalRow = document.querySelector('.total-row');

                const paymentMethodRow = paymentMethod.closest('tr');

                function resetCicilanFields() {
                    dpTypeSelect.value = '';
                    dpPercentageInput.value = 0;
                    dpNominalInput.value = 0;
                    downPaymentInput.value = 0;
                    remainingAmountInput.value = 0;

                    dpPercentageRow.style.display = 'none';
                    dpNominalRow.style.display = 'none';
                    totalRow.style.display = 'none';
                    paymentMethodRow.style.display = 'none';
                    bankListRow.style.display = 'none';
                    midtransButtonRow.style.display = 'none';
                    paymentProofRow.style.display = 'none';
                    amountPaidRow.style.display = 'none';
                }

                function updateDownPayment() {
                    let dpValue = 0;
                    if (dpTypeSelect.value === 'persen') {
                        dpValue = dealNegotiation * (parseFloat(dpPercentageInput.value || 0) / 100);
                    } else if (dpTypeSelect.value === 'nominal') {
                        dpValue = parseFloat(dpNominalInput.value || 0);
                    }
                    downPaymentInput.value = dpValue;
                    updateRemaining();
                    amountPaidRow.style.display = '';
                    paymentMethodRow.style.display = '';
                }

                function updateRemaining() {
                    const downPayment = parseFloat(downPaymentInput.value || 0);
                    const remaining = dealNegotiation - downPayment;
                    remainingAmountInput.value = remaining < 0 ? 0 : remaining;
                    totalRow.style.display = '';
                }

                paymentType.addEventListener('change', function() {
                    const value = this.value;
                    resetCicilanFields();

                    if (value === 'tunai') {
                        amountPaidInput.value = dealNegotiation;
                        amountPaidRow.style.display = '';
                        paymentMethodRow.style.display = '';
                        dpTypeRow.style.display = 'none';
                    } else if (value === 'cicilan') {
                        dpTypeRow.style.display = '';
                    }
                });

                dpTypeSelect.addEventListener('change', function() {
                    dpPercentageRow.style.display = 'none';
                    dpNominalRow.style.display = 'none';
                    totalRow.style.display = 'none';

                    if (this.value === 'persen') {
                        dpPercentageRow.style.display = '';
                    } else if (this.value === 'nominal') {
                        dpNominalRow.style.display = '';
                    }

                    updateDownPayment();
                });

                dpPercentageInput.addEventListener('input', updateDownPayment);
                dpNominalInput.addEventListener('input', updateDownPayment);
                downPaymentInput.addEventListener('input', updateRemaining);

                document.querySelectorAll('.quick-percent').forEach(btn => {
                    btn.addEventListener('click', function() {
                        dpPercentageInput.value = parseFloat(this.textContent);
                        updateDownPayment();
                    });
                });

                document.querySelectorAll('.quick-nominal').forEach(btn => {
                    btn.addEventListener('click', function() {
                        dpNominalInput.value = parseFloat(this.dataset.nominal);
                        updateDownPayment();
                    });
                });

                paymentMethod.addEventListener('change', function() {
                    const val = this.value;
                    bankListRow.style.display = val === 'transfer' ? '' : 'none';
                    midtransButtonRow.style.display = val === 'midtrans' ? '' : 'none';
                    paymentProofRow.style.display = (val === 'cash' || val === 'transfer') ? '' : 'none';
                });
            });
        </script>


        <!-- Midtrans -->
        <!-- Tambahkan di akhir sebelum </body> -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-VooMLjZdL3DUhthB"></script>
        <script>
            document.getElementById('midtrans_btn').addEventListener('click', function(e) {
                e.preventDefault();

                const orderId = this.dataset.orderId;
                const paymentType = document.querySelector('[name="payment_type"]').value;
                const downPayment = document.querySelector('[name="down_payment"]').value;
                const paymentMethod = document.querySelector('[name="payment_method"]').value;
                const remainingAmount = document.querySelector('[name="remaining_amount"]').value;

                fetch('save_transaction_ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            order_id: orderId,
                            payment_type: paymentType,
                            down_payment: downPayment,
                            payment_method: paymentMethod,
                            remaining_amount: remainingAmount
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.status === 'success') {
                            // lanjut ke Midtrans
                            fetch(`generate_snap_token.php?id=${orderId}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.snap_token) {
                                        window.snap.pay(data.snap_token, {
                                            onSuccess: function(result) {
                                                alert("Pembayaran berhasil!");
                                                location.reload();
                                            },
                                            onPending: function(result) {
                                                alert("Menunggu pembayaran.");
                                                location.reload();
                                            },
                                            onError: function(result) {
                                                alert("Terjadi kesalahan saat pembayaran.");
                                            }
                                        });
                                    } else {
                                        alert('Gagal generate token Midtrans');
                                    }
                                });
                        } else {
                            alert(response.message || 'Gagal menyimpan transaksi.');
                        }
                    });
            });
        </script>

        <script>
            function showEditDealForm() {
                document.getElementById('editDealForm').style.display = 'block';
                document.getElementById('editDealBtn').style.display = 'none';
            }

            function validateDealNegotiation(lowestPrice) {
                const input = document.getElementById('dealInput');
                const value = parseInt(input.value);
                if (value < lowestPrice) {
                    alert("Nilai negoisasi tidak boleh lebih rendah dari harga terendah!");
                    input.value = '';
                    input.focus();
                    return false;
                }
                return true;
            }
        </script>

    </div>
</div>

<?php include '../layout/footer.php'; ?>