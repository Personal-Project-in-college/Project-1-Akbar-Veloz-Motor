<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

// Asumsi user ID sudah ada di session setelah login
// checkLogin() harusnya sudah memastikan ini. Jika belum, pastikan ada.
// $_SESSION['user_id'] = 1; // Hapus baris ini jika sudah ada dari sistem login

$error = ''; // Variabel untuk menampung pesan error

// Permintaan #2: Ambil data kendaraan dengan nama modelnya menggunakan JOIN
$vehiclesQuery = $koneksi->query("
    SELECT v.id, vm.name AS model_name
    FROM vehicles AS v
    JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id
    WHERE v.status = 'available' AND v.deleted_at IS NULL
");
$vehicles = $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC);

// Ambil data semua customer untuk dropdown
$customersQuery = $koneksi->query("
    SELECT c.id, c.name
    FROM customers c
    WHERE c.deleted_at IS NULL
    AND NOT EXISTS (
        SELECT 1
        FROM test_drivers td
        JOIN orders o ON td.order_id = o.id
        WHERE o.customer_id = c.id
        AND td.status = 'process'
        AND o.deleted_at IS NULL
    )
    AND NOT EXISTS (
        SELECT 1
        FROM transactions t
        JOIN orders o ON t.order_id = o.id
        WHERE o.customer_id = c.id
        AND t.status = 'pending'
        AND o.deleted_at IS NULL
    )
    ORDER BY c.name ASC
");


// Proses form saat disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $date_order = $_POST['date_order'];
    $status_order = $_POST['status']; // Ganti nama variabel agar tidak bentrok dengan kolom

    // ====================================================================
    // Permintaan #1: Validasi Proses yang Sedang Berjalan
    // ====================================================================

    // A. Cek jika customer sudah punya test drive yang masih 'process'
    if ($status_order === 'test_driver') {
        // Asumsi: tabel `test_drivers` punya kolom `status` ENUM('process', 'done', 'cancelled')
        $checkTestDrive = $koneksi->prepare("
            SELECT COUNT(td.id)
            FROM test_drivers td
            JOIN orders o ON td.order_id = o.id
            WHERE o.customer_id = ? AND td.status = 'process' AND o.deleted_at IS NULL
        ");
        $checkTestDrive->execute([$customer_id]);
        if ($checkTestDrive->fetchColumn() > 0) {
            $error = "Customer ini sudah memiliki jadwal test drive yang sedang dalam proses. Selesaikan dulu proses tersebut.";
        }
    }

    // B. Cek jika customer sudah punya transaksi yang masih 'pending'
    if ($status_order === 'transaction') {
        $checkTransaction = $koneksi->prepare("
            SELECT COUNT(t.id)
            FROM transactions t
            JOIN orders o ON t.order_id = o.id
            WHERE o.customer_id = ? AND t.status = 'pending' AND o.deleted_at IS NULL
        ");
        $checkTransaction->execute([$customer_id]);
        if ($checkTransaction->fetchColumn() > 0) {
            $error = "Customer ini sudah memiliki transaksi yang masih pending. Selesaikan dulu transaksi tersebut.";
        }
    }

    // --- Akhir Validasi ---

    // Jika tidak ada error, lanjutkan proses penyimpanan
    if (empty($error)) {
        $koneksi->beginTransaction(); // Mulai transaksi database untuk keamanan data
        try {
            // 1. Insert ke tabel orders
            $insertOrderQuery = $koneksi->prepare("INSERT INTO orders (customer_id, vehicle_id, date_order, status, created_at) VALUES (?, ?, ?, ?, NOW())");
            $insertOrderQuery->execute([$customer_id, $vehicle_id, $date_order, $status_order]);
            $order_id = $koneksi->lastInsertId();

            $user_id = $_SESSION['user_id']; // Ambil user id dari session

            // 2. Buat record di tabel turunan (test_drivers atau transactions)
            if ($status_order === 'test_driver') {
                // Asumsi: tabel `test_drivers` ada kolom `status`
                $koneksi->prepare("INSERT INTO test_drivers (order_id, user_id, status, created_at) VALUES (?, ?, 'process', NOW())")->execute([$order_id, $user_id]);

                // 3. Update status kendaraan
                $koneksi->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?")->execute([$vehicle_id]);
            }

            if ($status_order === 'transaction') {
                // Langsung buat record transaksi dengan status 'pending' agar validasi berfungsi
                $koneksi->prepare("INSERT INTO transactions (order_id, user_id, status, grand_total, amount_paid, payment_type, payment_method, created_at) VALUES (?, ?, 'pending', 0, 0, 'tunai', 'cash', NOW())")
                    ->execute([$order_id, $user_id]);

                // 3. Update status kendaraan
                $koneksi->prepare("UPDATE vehicles SET status = 'transaction' WHERE id = ?")->execute([$vehicle_id]);
            }

            $koneksi->commit(); // Simpan semua perubahan jika semua query berhasil

            $_SESSION['success_message'] = "Pesanan baru berhasil ditambahkan.";
            header("Location: orders.php"); // Arahkan kembali ke halaman daftar order
            exit;
        } catch (Exception $e) {
            $koneksi->rollBack(); // Batalkan semua perubahan jika ada error di tengah jalan
            $error = "Terjadi kesalahan saat menyimpan data: " . $e->getMessage();
        }
    }
}

// Ambil tanggal dan waktu saat ini untuk nilai default di form
$now = date('Y-m-d\TH:i');

// --- Bagian Tampilan (View) ---
// diasumsikan file layout ada di path yang benar
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
                        <label for="date_order" class="form-label">Waktu Pesan</label>
                        <input type="datetime-local" class="form-control" id="date_order" name="date_order" value="<?= htmlspecialchars($_POST['loan_date'] ?? $now) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status Pesanan</label>
                        <select name="status" class="form-control" required style="color: black;" required>
                            <option value="test_driver">Test Driver</option>
                            <option value="transaction">Transaksi</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="orders.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>