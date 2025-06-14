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

// Ambil data lengkap test_driver dan relasi order, customer, vehicle
$query = $koneksi->prepare("
    SELECT 
        td.*, 
        o.type_order,
        c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address,
        v.id AS vehicle_id, vm.name AS vehicle_model_name
    FROM test_drivers td
    JOIN orders o ON td.order_id = o.id
    JOIN customers c ON o.customer_id = c.id
    JOIN vehicles v ON o.vehicle_id = v.id
    JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
    WHERE td.order_id = ? AND td.deleted_at IS NULL
");
$query->execute([$id]);
$test_driver = $query->fetch(PDO::FETCH_ASSOC);

if (!$test_driver) {
    $_SESSION['danger_message'] = "<strong>Error:</strong> Data tidak ditemukan atau sudah dihapus.";
    header("Location: ../orders/orders.php");
    exit;
}

// Ambil tambahan data kendaraan
$vehicleQuery = $koneksi->prepare("SELECT v.*, vm.name as model_name FROM vehicles v JOIN vehicle_models vm ON v.vehicle_model_id = vm.id WHERE v.id = ?");
$vehicleQuery->execute([$test_driver['vehicle_id']]);
$vehicle = $vehicleQuery->fetch(PDO::FETCH_ASSOC);

// Hitung sisa hari STNK
$stnk_deadline = new DateTime($vehicle['stnk_deadline']);
$today = new DateTime();
$interval = $today->diff($stnk_deadline);
$sisaHari = $interval->days . " hari (" . ($stnk_deadline > $today ? "tersisa" : "lewat") . ")";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status']);
    $result_note = trim($_POST['result_note']);

    // Update status test driver
    $updateQuery = $koneksi->prepare("UPDATE test_drivers SET status = ?, result_note = ?, updated_at = NOW() WHERE id = ?");
    $updateQuery->execute([$status, $result_note, $test_driver['id']]);

    // Jika status 'cancelled' atau 'finish', update juga status di orders
    if ($status === 'cancelled' || $status === 'finish') {
        $updateOrder = $koneksi->prepare("UPDATE orders SET status = 'finished', updated_at = NOW() WHERE id = ?");
        $updateOrder->execute([$test_driver['order_id']]);
    }

    $_SESSION['success_message'] = "Pesanan <strong>{$test_driver['customer_name']}</strong> telah diselesaikan.";
    header("Location: ../orders/orders.php");
    exit;
}
// Mapping ke Bahasa Indonesia
$translateTypeVehicle = [
    'motorcycle' => 'Motor',
    'car' => 'Mobil',
];

$translateFuel = [
    'gasoline' => 'Bensin',
    'electric' => 'Listrik',
    'hybrid' => 'Hibrida'
];

$tahunProduksi = (int)$vehicle['production_year'];
$tahunSekarang = (int)date('Y');
$umurKendaraan = $tahunSekarang - $tahunProduksi;

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<style>
    td {
        word-break: break-word;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Coba Kendaraan</h3>

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
                        <td><?= $translateTypeVehicle[$vehicle['type_vehicle']] ?? $vehicle['type_vehicle'] ?></td>
                    </tr>

                    <tr>
                        <th>Warna</th>
                        <td><?= htmlspecialchars($vehicle['color']) ?></td>
                    </tr>
                    <tr>
                        <th>Tahun Produksi</th>
                        <td>
                            <?= htmlspecialchars($vehicle['production_year']) ?><br>
                            <small class="text-muted"><?= $umurKendaraan ?> tahun sejak diproduksi</small>
                        </td>
                    </tr>

                    <tr>
                        <th>Pajak STNK</th>
                        <td><?= htmlspecialchars($vehicle['stnk_deadline']) ?> <br><small class="text-muted"><?= $sisaHari ?></small></td>
                    </tr>
                    <tr>
                        <th>Bahan Bakar</th>
                        <td><?= $translateFuel[$vehicle['type_fuel']] ?? $vehicle['type_fuel'] ?></td>
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


        <div class="card">
            <div class="card-body">
                <h5 class="mb-4">Edit Hasil Coba Kendaraan</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label for="result_note" class="form-label">Catatan Hasil</label>
                        <textarea class="form-control" id="result_note" name="result_note" autofocus rows="8"><?= htmlspecialchars($test_driver['result_note'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" style="color: black;" required>
                            <option value="finish" <?= $test_driver['status'] === 'finish' ?>>Selesai</option>
                            <option value="cancelled" <?= $test_driver['status'] === 'cancelled' ?>>Dibatalkan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="../orders/orders.php" class="btn btn-secondary mx-2 text-white">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>