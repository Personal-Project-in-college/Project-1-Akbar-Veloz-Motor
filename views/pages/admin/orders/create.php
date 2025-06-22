<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/sendOrderEmailCustomer.php';
checkLogin();

$error = '';

$vehiclesQuery = $koneksi->query("
    SELECT v.id, vm.name AS model_name, b.name AS brand_name
    FROM vehicles AS v
    JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id
    JOIN brands AS b ON vm.brand_id = b.id
    WHERE v.status = 'available' AND v.deleted_at IS NULL
");
$vehicles = $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC);

$customers = $koneksi->query("
    SELECT c.id, c.name, c.email, c.address
    FROM customers c
    WHERE c.deleted_at IS NULL
    AND NOT EXISTS (
        SELECT 1 FROM test_drivers td
        JOIN orders o ON td.order_id = o.id
        WHERE o.customer_id = c.id AND td.status = 'process' AND o.deleted_at IS NULL
    )
    AND NOT EXISTS (
        SELECT 1 FROM transactions t
        JOIN orders o ON t.order_id = o.id
        WHERE o.customer_id = c.id AND t.status = 'pending' AND o.deleted_at IS NULL
    )
    ORDER BY c.name ASC
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $type_order = $_POST['type_order'];
    $type_arrival = $_POST['type_arrival'];
    $order_date = empty($_POST['order_date']) ? date('Y-m-d H:i:s') : $_POST['order_date'];

    $customer = null;
    foreach ($customers as $c) {
        if ($c['id'] == $customer_id) {
            $customer = $c;
            break;
        }
    }

    $vehicle = null;
    foreach ($vehicles as $v) {
        if ($v['id'] == $vehicle_id) {
            $vehicle = $v;
            break;
        }
    }

    // Validasi
    if ($type_order === 'test_driver') {
        $check = $koneksi->prepare("SELECT COUNT(*) FROM test_drivers td JOIN orders o ON td.order_id = o.id WHERE o.customer_id = ? AND td.status = 'process' AND o.deleted_at IS NULL");
        $check->execute([$customer_id]);
        if ($check->fetchColumn() > 0) {
            $error = "Customer ini sudah memiliki test drive yang aktif.";
        }
    }

    if ($type_order === 'transaction') {
        $check = $koneksi->prepare("SELECT COUNT(*) FROM transactions t JOIN orders o ON t.order_id = o.id WHERE o.customer_id = ? AND t.status = 'pending' AND o.deleted_at IS NULL");
        $check->execute([$customer_id]);
        if ($check->fetchColumn() > 0) {
            $error = "Customer ini sudah memiliki transaksi yang masih pending.";
        }
    }

    if (empty($error)) {
        try {
            $koneksi->beginTransaction();
            $stmt = $koneksi->prepare("INSERT INTO orders (customer_id, vehicle_id, type_order, type_arrival, order_date, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$customer_id, $vehicle_id, $type_order, $type_arrival, $order_date]);
            $order_id = $koneksi->lastInsertId();
            $user_id = $_SESSION['user_id'];

            if ($type_order === 'test_driver') {
                $stmt = $koneksi->prepare("INSERT INTO test_drivers (order_id, user_id, status, created_at) VALUES (?, ?, 'process', NOW())");
                $stmt->execute([$order_id, $user_id]);
                $koneksi->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?")->execute([$vehicle_id]);
            }

            if ($type_order === 'transaction') {
                $getPriceStmt = $koneksi->prepare("SELECT price_displayed FROM vehicles WHERE id = ?");
                $getPriceStmt->execute([$vehicle_id]);
                $price = $getPriceStmt->fetchColumn();
                $koneksi->prepare("INSERT INTO transactions (order_id, user_id, vehicle_price, deal_negotiation, status, created_at) VALUES (?, ?, ?, 0, 'pending', NOW())")
                    ->execute([$order_id, $user_id, $price]);
                $koneksi->prepare("UPDATE vehicles SET status = 'transaction' WHERE id = ?")->execute([$vehicle_id]);
            }

            // 🔔 Kirim Email ke Customer
            $link = "http://project-1-akbar-veloz-motor.com/detail-pesanan.php?id={$order_id}";
            sendEmailToCustomer(
                $customer['email'],
                $customer['name'],
                $vehicle_id,
                $vehicle['brand_name'],
                $vehicle['model_name'],
                $type_order,
                $order_date,
                $type_arrival,
                $customer['address'],
                $link
            );

            $koneksi->commit();
            $_SESSION['success_message'] = "Pesanan berhasil ditambahkan dan email dikirim.";
            header("Location: orders.php");
            exit;
        } catch (Exception $e) {
            $koneksi->rollBack();
            $error = "Terjadi error: " . $e->getMessage();
        }
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Pesanan</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="create.php">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select id="customer_id" name="customer_id" class="form-control" required style="color: black;">
                            <option value="">Pilih Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Kendaraan</label>
                        <select id="vehicle_id" name="vehicle_id" class="form-control" required style="color: black;">
                            <option value="">Pilih Kendaraan</option>
                            <!-- Permintaan #2: Tampilkan ID dan Model Kendaraan -->
                            <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?= $vehicle['id'] ?>">
                                    <?= $vehicle['id'] ?> | <?= htmlspecialchars($vehicle['model_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="type_order" class="form-label">Tipe Pesanan</label>
                        <select name="type_order" class="form-control" required style="color: black;">
                            <option value="">Pilih Tipe Pesanan</option>
                            <option value="test_driver">Test Driver</option>
                            <option value="transaction">Transaksi</option>
                        </select>
                    </div>


                    <div class="mb-3" id="test_driver_type_group" style="display:none;">
                        <label for="type_arrival" class="form-label">Tipe Test Driver</label>
                        <select name="type_arrival" class="form-control" style="color: black;">
                            <option value="showroom">Customer Datang Ke Showroom</option>
                            <option value="home_visit">Karyawan Datang Ke Alamat Customers</option>
                        </select>
                    </div>

                    <div class="mb-3" id="test_driver_date_group" style="display:none;">
                        <label for="order_date" class="form-label">Tanggal Test Drive</label>
                        <input type="datetime-local" class="form-control" id="order_date" name="order_date" placeholder="Masukan Tanggal Test Drive">
                    </div>


                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="orders.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>

    <script>
        const typeOrderSelect = document.querySelector('select[name="type_order"]');
        const testDriverTypeGroup = document.getElementById('test_driver_type_group');
        const testDriverDateGroup = document.getElementById('test_driver_date_group');

        function toggleTestDriverType() {
            if (typeOrderSelect.value === 'test_driver') {
                testDriverTypeGroup.style.display = 'block';
                testDriverDateGroup.style.display = 'block';
            } else {
                testDriverTypeGroup.style.display = 'none';
                testDriverDateGroup.style.display = 'none';
            }
        }

        typeOrderSelect.addEventListener('change', toggleTestDriverType);
        toggleTestDriverType(); // jalankan saat awal
    </script>

</div>