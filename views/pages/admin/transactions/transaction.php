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

$query = $koneksi->prepare("SELECT transaction.*, o.type_order, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address, v.id AS vehicle_id, vm.name AS vehicle_model_name FROM transactions transaction JOIN orders o ON transaction.order_id = o.id JOIN customers c ON o.customer_id = c.id JOIN vehicles v ON o.vehicle_id = v.id JOIN vehicle_models vm ON v.vehicle_model_id = vm.id WHERE transaction.order_id = ? AND transaction.deleted_at IS NULL");
$query->execute([$id]);
$transaction = $query->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    $_SESSION['danger_message'] = "<strong>Error:</strong> Data tidak ditemukan atau sudah dihapus.";
    header("Location: ../orders/orders.php");
    exit;
}

$vehicleQuery = $koneksi->prepare("SELECT v.*, vm.name as model_name FROM vehicles v JOIN vehicle_models vm ON v.vehicle_model_id = vm.id WHERE v.id = ?");
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
        header("Location: ../orders/orders.php");
        exit;
    } else {
        $order_id = $_POST['order_id'];
        $deal_negotiation = $_POST['deal_negotiation'];
        $grand_total = $_POST['grand_total'];
        $payment_type = $_POST['payment_type'];
        $amount_paid = $_POST['amount_paid'];
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

            // Cek apakah ada file yang diupload
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                $paymentProofFile = $_FILES['payment_proof'];
                $ext = pathinfo($paymentProofFile['name'], PATHINFO_EXTENSION);
                $filename = 'payment_proof_' . time() . '.' . $ext;
                $fullPath = $basePath . '/' . $filename;
                $relativePath = str_replace('../../../../', '', $fullPath);

                move_uploaded_file($paymentProofFile['tmp_name'], $fullPath);

                // Update dengan bukti pembayaran
                $stmt = $koneksi->prepare("UPDATE transactions 
                    SET deal_negotiation = ?, grand_total = ?, payment_type = ?, amount_paid = ?, 
                        payment_method = ?, payment_proof = ?, status = ?, updated_at = NOW() 
                    WHERE order_id = ?");
                $stmt->execute([$deal_negotiation, $grand_total, $payment_type, $amount_paid, $payment_method, $relativePath, $status, $order_id]);
            } else {
                // Update tanpa bukti pembayaran
                $stmt = $koneksi->prepare("UPDATE transactions 
                    SET deal_negotiation = ?, grand_total = ?, payment_type = ?, amount_paid = ?, 
                        payment_method = ?, status = ?, updated_at = NOW() 
                    WHERE order_id = ?");
                $stmt->execute([$deal_negotiation, $grand_total, $payment_type, $amount_paid, $payment_method, $status, $order_id]);
            }

            $_SESSION['success_message'] = "Transaksi berhasil diperbarui.";
            header("Location: checkout.php?order_id=" . $order_id);
            exit;
        } catch (PDOException $e) {
            $_SESSION['danger_message'] = "Gagal menyimpan transaksi: " . $e->getMessage();
            header("Location: transaction.php?id=" . $order_id);
            exit;
        }
    }
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
                        <th class="w-25">Tipe</th>
                        <td><?= htmlspecialchars($vehicle['type_vehicle']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Warna</th>
                        <td><?= htmlspecialchars($vehicle['color']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Tahun Produksi</th>
                        <td><?= htmlspecialchars($vehicle['production_year']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">STNK Deadline</th>
                        <td><?= htmlspecialchars($vehicle['stnk_deadline']) ?> <br><small class="text-muted"><?= $sisaHari ?></small></td>
                    </tr>
                    <tr>
                        <th class="w-25">Bahan Bakar</th>
                        <td><?= htmlspecialchars($vehicle['type_fuel']) ?></td>
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
                <form method="POST">
                    <input type="hidden" name="assign_user" value="1">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Petugas</label>
                        <select name="user_id" id="user_id" class="form-select" required style="color: black;">
                            <option value="">-- Pilih Petugas --</option>
                            <?php foreach ($staffList as $staff): ?>
                                <option value="<?= $staff['id'] ?>" <?= ($transaction['user_id'] == $staff['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($staff['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>

        <!-- TABEL NEGOISASI -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">Negosiasi Harga</h5>
                    <input type="hidden" name="order_id" value="<?= $transaction['order_id'] ?>">

                    <div class="mb-3">
                        <label>Harga Kendaraan</label>
                        <input type="text" id="vehicle_price" class="form-control" readonly value="<?= number_format($transaction['vehicle_price'], 0, ',', '.') ?>">
                    </div>
                    <div class="mb-3">
                        <label>Deal Negosiasi<span class="text-danger">*</span></label>
                        <input type="number" id="deal_negotiation" name="deal_negotiation" class="form-control" value="<?= $transaction['deal_negotiation'] ?>">
                    </div>
                    <button type="submit" name="submit_deal" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>


        <!-- TABEL TRANSAKSI -->
        <?php if ($alreadyAssigned && $transaction['deal_negotiation'] > 0): ?>
            <div class="card mb-4">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" value="<?= $transaction['order_id'] ?>">
                    <div class="card-body">
                        <h5 class="mb-4">Transaksi</h5>
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
                            <tr class="cicilan-row total-row" style="display:none">
                                <th class="w-25">Total Uang Muka</th>
                                <td><input style="border: 0;" type="text" id="dp_total" class="form-control" readonly value="0"></td>
                            </tr>
                            <tr class="cicilan-row">
                                <th class="w-25">Jumlah Dibayarkan<span class="text-danger">*</span></th>
                                <td><input style="border: 0;" type="number" name="amount_paid" class="form-control" value="0"></td>
                            </tr>
                            <tr>
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
                                <td>
                                    <a href="#"
                                        id="midtrans_btn"
                                        class="btn btn-primary text-white"
                                        data-order-id="<?= $transaction['order_id'] ?>">Bayar via Midtrans</a>
                                </td>
                            </tr>

                            <tr id="payment_proof_button_row" style="display:none">
                                <th class="w-25">Bukti Pembayaran<span class="text-danger">*</span></th>
                                <td><input type="file" name="payment_proof" accept="image/*" class="form-control" required></td>
                            </tr>
                        </table>

                        <a href="partner.php" class="mt-3 btn btn-danger text-white">Batalkan Transaksi</a>
                        <button type="submit" class="mt-3 mx-2 btn btn-primary">Simpan</button>

                    </div>
                </form>
            </div>
        <?php endif; ?>

        <script>
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
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

            function updateDP() {
                let total = parseFloat(grandTotalHidden.value) || 0;
                let type = document.getElementById('dp_type').value;
                let result = 0;

                if (type === 'persen') {
                    let percent = parseFloat(document.getElementById('dp_percentage').value) || 0;
                    result = total * (percent / 100);
                } else if (type === 'nominal') {
                    let nominal = parseFloat(document.getElementById('dp_nominal').value) || 0;
                    result = nominal;
                }

                document.getElementById('dp_total').value = formatRupiah(result);

                // Otomatis isi jumlah dibayarkan
                const amountPaidInput = document.querySelector('input[name="amount_paid"]');
                amountPaidInput.value = result.toFixed(0);
            }

            document.addEventListener('DOMContentLoaded', updateGrandTotal);
            negoInput.addEventListener('input', updateGrandTotal);

            document.getElementById('payment_type').addEventListener('change', function() {
                let cicilanRows = document.querySelectorAll('.cicilan-row');
                cicilanRows.forEach(row => row.style.display = this.value === 'cicilan' ? '' : 'none');

                document.getElementById('dp_type').value = '';
                document.querySelector('.persen-row').style.display = 'none';
                document.querySelector('.nominal-row').style.display = 'none';
                document.querySelector('.total-row').style.display = 'none';
            });

            document.getElementById('dp_type').addEventListener('change', function() {
                let persen = document.querySelector('.persen-row');
                let nominal = document.querySelector('.nominal-row');
                let totalRow = document.querySelector('.total-row');

                persen.style.display = this.value === 'persen' ? '' : 'none';
                nominal.style.display = this.value === 'nominal' ? '' : 'none';
                totalRow.style.display = this.value ? '' : 'none';

                updateDP();
            });

            document.getElementById('dp_percentage').addEventListener('input', updateDP);
            document.getElementById('dp_nominal').addEventListener('input', updateDP);

            document.querySelectorAll('.quick-percent').forEach(btn => {
                btn.addEventListener('click', function() {
                    let val = parseInt(this.innerText);
                    document.getElementById('dp_percentage').value = val;
                    updateDP();
                });
            });

            document.querySelectorAll('.quick-nominal').forEach(btn => {
                btn.addEventListener('click', function() {
                    let val = parseInt(this.getAttribute('data-nominal'));
                    document.getElementById('dp_nominal').value = val;
                    updateDP();
                });
            });

            document.getElementById('payment_method').addEventListener('change', function() {
                const value = this.value;
                document.getElementById('bank_list_row').style.display = value === 'transfer' ? '' : 'none';
                document.getElementById('midtrans_button_row').style.display = value === 'midtrans' ? '' : 'none';
                document.getElementById('payment_proof_button_row').style.display = (value === 'cash' || value === 'transfer') ? '' : 'none';
            });
        </script>

        <!-- Midtrans -->
        <!-- Tambahkan di akhir sebelum </body> -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-VooMLjZdL3DUhthB"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const midtransBtn = document.getElementById("midtrans_btn");

                if (midtransBtn) {
                    midtransBtn.addEventListener("click", function(e) {
                        e.preventDefault();

                        const orderId = this.getAttribute("data-order-id");

                        if (!orderId) {
                            alert("Order ID tidak ditemukan.");
                            return;
                        }

                        fetch(`generate_snap_token.php?id=${orderId}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.snap_token) {
                                    snap.pay(data.snap_token, {
                                        onSuccess: function(result) {
                                            alert("Pembayaran berhasil!");
                                            location.reload();
                                        },
                                        onPending: function(result) {
                                            alert("Pembayaran sedang diproses...");
                                        },
                                        onError: function(result) {
                                            alert("Pembayaran gagal.");
                                        }
                                    });
                                } else if (data.error) {
                                    alert("Gagal generate token: " + data.error);
                                } else {
                                    alert("Gagal generate token: response tidak dikenali.");
                                }
                            })
                            .catch(err => {
                                console.error("Error:", err);
                                alert("Gagal menghubungi server.");
                            });
                    });
                }
            });
        </script>
    </div>
</div>

<?php include '../layout/footer.php'; ?>