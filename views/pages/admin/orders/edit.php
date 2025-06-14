<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

// Pastikan order_id tersedia
if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}

$order_id = $_GET['id'];
$error = '';

// Ambil data order lama beserta nama customer
$orderQuery = $koneksi->prepare("SELECT o.*, c.name AS customer_name FROM orders o JOIN customers c ON o.customer_id = c.id WHERE o.id = ? AND o.deleted_at IS NULL");
$orderQuery->execute([$order_id]);
$order = $orderQuery->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['error_message'] = "Data order tidak ditemukan.";
    header("Location: orders.php");
    exit;
}

// Ambil kendaraan yang tersedia untuk dipilih
$vehiclesQuery = $koneksi->query("
    SELECT v.id, vm.name AS model_name
    FROM vehicles AS v
    JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id
    WHERE v.status = 'available' AND v.deleted_at IS NULL
");
$vehicles = $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC);

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $date_order = $_POST['date_order'];

    try {
        $koneksi->beginTransaction();

        // Update data order
        $updateQuery = $koneksi->prepare("UPDATE orders SET vehicle_id = ?, date_order = ? WHERE id = ?");
        $updateQuery->execute([$vehicle_id, $date_order, $order_id]);

        // Update status kendaraan baru
        $status = $order['type_order'] === 'test_driver' ? 'test_drive' : 'transaction';
        $koneksi->prepare("UPDATE vehicles SET status = ? WHERE id = ?")
            ->execute([$status, $vehicle_id]);

        $koneksi->commit();
        $_SESSION['success_message'] = "Data order berhasil diperbarui.";
        header("Location: orders.php");
        exit;
    } catch (Exception $e) {
        $koneksi->rollBack();
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Pesanan</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($order['customer_name']) ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Kendaraan</label>
                        <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                            <option value="">Pilih Kendaraan</option>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?= $vehicle['id'] ?>" <?= $vehicle['id'] == $order['vehicle_id'] ? 'selected' : '' ?>>
                                    <?= $vehicle['id'] ?> | <?= htmlspecialchars($vehicle['model_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date_order" class="form-label">Waktu Pesan</label>
                        <input type="datetime" class="form-control" name="date_order" id="date_order" value="<?= date($order['date_order']) ?>" required>
                        
                    </div>

                    <div class="mb-3">
                        <label for="type_order" class="form-label">Tipe Pesanan</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($order['type_order']) ?>" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="orders.php" class="btn btn-secondary mx-2 text-white">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>