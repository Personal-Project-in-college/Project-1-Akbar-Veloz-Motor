<?php

session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

if (!isset($_GET['id'])) {
    $_SESSION['danger_message'] = "<strong>Error: </strong>ID peminjaman kendaraan tidak ditemukan di URL.";
    header('Location: vehicle_loans.php');
    exit;
}

$id = $_GET['id'];

// Ambil data lengkap peminjaman kendaraan
$getVehicleLoanQuery = $koneksi->prepare("SELECT 
    vehicle_loans.*,
    partners.name AS partner_name,
    partners.phone AS partner_phone,
    partners.email AS partner_email,
    vehicles.id AS vehicle_id,
    vehicles.type_vehicle,
    vehicles.color,
    vehicles.status AS vehicle_status,
    vehicle_models.name AS model_name,
    brands.name AS brand_name,
    branches.name AS branch_name,
    branches.address AS branch_address
FROM vehicle_loans
LEFT JOIN partners ON vehicle_loans.partner_id = partners.id
LEFT JOIN vehicles ON vehicle_loans.vehicle_id = vehicles.id
LEFT JOIN vehicle_models ON vehicles.vehicle_model_id = vehicle_models.id
LEFT JOIN brands ON vehicle_models.brand_id = brands.id
LEFT JOIN branches ON vehicles.branch_id = branches.id
WHERE vehicle_loans.id = ?");
$getVehicleLoanQuery->execute([$id]);
$vehicle = $getVehicleLoanQuery->fetch();

if (!$vehicle) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data peminjaman kendaraan tidak ditemukan atau sudah dihapus.";
    header('Location: vehicle_loans.php');
    exit;
}

$types = [
    'car' => 'Mobil',
    'motorcycle' => 'Motor',
];

$statuses = [
    'borrowed' => 'Dipinjam',
    'returned' => 'Dikembalikan',
];

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Peminjaman Kendaraan</h3>
        <!-- TABEL Peminjaman -->
        <div>
            <!-- Desktop View -->
            <div class="card mb-4 d-none d-sm-block">
                <div class="card-body">
                    <h5 class="mb-4">Informasi Peminjaman</h5>
                    <table class="table">
                        <tr>
                            <th class="w-25">Nama</th>
                            <td><?= $vehicle['partner_name'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Phone</th>
                            <td><?= $vehicle['partner_phone'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Email</th>
                            <td><?= $vehicle['partner_email'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Waktu Pinjam</th>
                            <td><?= $vehicle['loan_date'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Waktu Kembali</th>
                            <td><?= $vehicle['return_date'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Catatan</th>
                            <td class="text-wrap"><?= $vehicle['note'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Status</th>
                            <td class="text-wrap"><?= $vehicle['status'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Mobile View -->
            <div class="card mb-4 d-block d-sm-none">
                <div class="card-body">
                    <h5 class="mb-4 text-center">Informasi Peminjaman</h5>

                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Nama</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['partner_name']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Phone</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['partner_phone']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Email</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['partner_email']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Waktu Pinjam</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['loan_date']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Waktu Kembali</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['return_date']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Alamat</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($vehicle['note']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Status</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['status']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL Brand & Model -->
        <div>
            <!-- Desktop View -->
            <div class="card mb-4 d-none d-sm-block">
                <div class="card-body">
                    <h5 class="mb-4">Informasi Merek</h5>
                    <table class="table">
                        <tr>
                            <th class="w-25">Merek</th>
                            <td><?= $vehicle['brand_name'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Model</th>
                            <td><?= $vehicle['model_name'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Mobile View -->
            <div class="card mb-4 d-block d-sm-none">
                <div class="card-body">
                    <h5 class="mb-4 text-center">Informasi Merek</h5>

                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Merek</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['brand_name']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Model</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($vehicle['model_name']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL KENDARAAN -->
        <div>
            <!-- Desktop View -->
            <div class="card mb-4 d-none d-sm-block">
                <div class="card-body">
                    <h5 class="mb-4">Informasi Kendaraan</h5>
                    <table class="table">
                        <tr>
                            <th class="w-25">Kode</th>
                            <td><?= htmlspecialchars($vehicle['id']) ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Tipe</th>
                            <td><?= $types[$vehicle['type_vehicle']] ?? '-' ?> <i class="<?= getVehicleIcon($vehicle['type_vehicle']) ?>"></i></td>
                        </tr>
                        <tr>
                            <th class="w-25">Warna</th>
                            <td><?= htmlspecialchars($vehicle['color']) ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Status</th>
                            <td><?= $statuses[$vehicle['status']] ?? '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Mobile View -->
            <div class="card mb-4 d-block d-sm-none">
                <div class="card-body">
                    <h5 class="mb-4 text-center">Informasi Kendaraan</h5>
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Kode</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['id']) ?></div>
                    </div>

                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Tipe</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= $types[$vehicle['type_vehicle']] ?? '-' ?> <i class="<?= getVehicleIcon($vehicle['type_vehicle']) ?>"></i></div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Status</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= $statuses[$vehicle['status']]  ?></div>
                    </div>

                </div>
            </div>
        </div>

        <!-- TABEL Cabang -->
        <div>
            <!-- Desktop View -->
            <div class="card mb-4 d-none d-sm-block">
                <div class="card-body">
                    <h5 class="mb-4">Informasi Cabang</h5>
                    <table class="table">
                        <tr>
                            <th class="w-25">Nama</th>
                            <td><?= $vehicle['branch_name'] ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Alamat</th>
                            <td class="text-wrap"><?= $vehicle['branch_address'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Mobile View -->
            <div class="card mb-4 d-block d-sm-none">
                <div class="card-body">
                    <h5 class="mb-4 text-center">Informasi Cabang</h5>

                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Nama</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['branch_name']) ?></div>
                    </div>
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Alamat</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($vehicle['branch_address']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>