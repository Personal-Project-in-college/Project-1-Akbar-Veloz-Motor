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
$currentUserId    = $_SESSION['user_id'];
$currentUserName  = $_SESSION['name'];
$currentUserPhone = $_SESSION['phone'];
$currentUserRole  = $_SESSION['role_id'];

// Ambil tambahan data kendaraan
$vehicleQuery = $koneksi->prepare("SELECT v.*, vm.name as model_name FROM vehicles v JOIN vehicle_models vm ON v.vehicle_model_id = vm.id WHERE v.id = ?");
$vehicleQuery->execute([$test_driver['vehicle_id']]);
$vehicle = $vehicleQuery->fetch(PDO::FETCH_ASSOC);

// Hitung sisa hari STNK
$stnk_deadline = new DateTime($vehicle['stnk_deadline']);
$today = new DateTime();
$interval = $today->diff($stnk_deadline);
$sisaHari = $interval->days . " hari (" . ($stnk_deadline > $today ? "tersisa" : "lewat") . ")";

// Ambil list user dengan role = 2 (misal: Petugas Test Drive)
$staffQuery = $koneksi->prepare("SELECT id, name FROM users WHERE role_id = 2 AND deleted_at IS NULL");
$staffQuery->execute();
$staffList = $staffQuery->fetchAll(PDO::FETCH_ASSOC);

// Kondisi apakah user_id sudah terisi (test driver sudah dilayani)
$alreadyAssigned = !empty($test_driver['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_user'])) {
        // Simpan petugas
        $user_id = $_POST['user_id'];
        $updateStaff = $koneksi->prepare("UPDATE test_drivers SET user_id = ?, updated_at = NOW() WHERE id = ?");
        $updateStaff->execute([$user_id, $test_driver['id']]);
        $_SESSION['success_message'] = "Petugas telah ditugaskan.";
        header("Location: ../orders/orders.php");
        exit;
    } else {
        // Simpan hasil dan status
        $status = trim($_POST['status']);
        $result_note = trim($_POST['result_note']);

        $updateQuery = $koneksi->prepare("UPDATE test_drivers SET status = ?, result_note = ?, updated_at = NOW() WHERE id = ?");
        $updateQuery->execute([$status, $result_note, $test_driver['id']]);

        if ($status === 'cancelled' || $status === 'finish') {
            $updateOrder = $koneksi->prepare("UPDATE orders SET status = 'finished', updated_at = NOW() WHERE id = ?");
            $updateOrder->execute([$test_driver['order_id']]);

            $updateVehicle = $koneksi->prepare("UPDATE vehicles SET status = 'available', updated_at = NOW() WHERE id = ?");
            $updateVehicle->execute([$test_driver['vehicle_id']]);
        }

        $_SESSION['success_message'] = "Pesanan <strong>{$test_driver['customer_name']}</strong> telah diselesaikan.";
        header("Location: ../orders/orders.php");
        exit;
    }
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
                        <th class="w-25">Nama</th>
                        <td><?= htmlspecialchars($test_driver['customer_name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Email</th>
                        <td><?= htmlspecialchars($test_driver['customer_email']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Nomor HP</th>
                        <td><?= htmlspecialchars($test_driver['customer_phone'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Alamat</th>
                        <td class="text-wrap"><?= htmlspecialchars($test_driver['customer_address'] ?? '-') ?></td>
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
                        <td><?= $translateTypeVehicle[$vehicle['type_vehicle']] ?? $vehicle['type_vehicle'] ?></td>
                    </tr>

                    <tr>
                        <th class="w-25">Warna</th>
                        <td><?= htmlspecialchars($vehicle['color']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Tahun Produksi</th>
                        <td>
                            <?= htmlspecialchars($vehicle['production_year']) ?><br>
                            <small class="text-muted"><?= $umurKendaraan ?> tahun sejak diproduksi</small>
                        </td>
                    </tr>

                    <tr>
                        <th class="w-25">Pajak STNK</th>
                        <td><?= htmlspecialchars($vehicle['stnk_deadline']) ?> <br><small class="text-muted"><?= $sisaHari ?></small></td>
                    </tr>
                    <tr>
                        <th class="w-25">Bahan Bakar</th>
                        <td><?= $translateFuel[$vehicle['type_fuel']] ?? $vehicle['type_fuel'] ?></td>
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

                <?php if ($test_driver['user_id'] == null): ?>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="assign_user" value="1">
                        <input type="hidden" name="user_id" value="<?= $currentUserId ?>">
                        <button type="submit" class="btn btn-primary">Saya Akan Menangani</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>


        <?php if ($alreadyAssigned): ?>
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
                                <option value="process" <?= $test_driver['status'] === 'process' ? 'selected' : '' ?>>Proses</option>
                                <option value="finish" <?= $test_driver['status'] === 'finish' ? 'selected' : '' ?>>Selesai</option>
                                <option value="cancelled" <?= $test_driver['status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="../orders/orders.php" class="btn btn-secondary mx-2 text-white">Kembali</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../layout/footer.php'; ?>