<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

// 🔎 Melakukan Pengecekan Apakah Sudah Login atau Belum
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

include '../../../../helpers/functionShowAlert.php';

$id = $_GET['id'] ?? null;
// 🪢 Ambil id vehicle dari URL

if (!$id) {
    die("Id tidak ditemukan.");
}

// 🪢 Ambil data vehicle berdasarkan Id
$data = $koneksi->prepare("
    SELECT 
        vehicles.*, 
        vehicle_models.name AS model_name, 
        brands.name AS brand_name,
        branches.name AS branch_name,
        branches.address AS branch_address
    FROM vehicles
    JOIN vehicle_models ON vehicles.vehicle_model_id = vehicle_models.id
    JOIN brands ON vehicle_models.brand_id = brands.id
    JOIN branches ON vehicles.branch_id = branches.id
    WHERE vehicles.id = ?
");
$data->execute([$id]);
$vehicle = $data->fetch(PDO::FETCH_ASSOC);

$getdata = $koneksi->prepare("SELECT * FROM vehicle_documents WHERE vehicle_id = ? AND deleted_at IS NULL");
$getdata->execute([$id]);
$vehicle_id = $getdata->fetch(PDO::FETCH_ASSOC);

$getdataPhoto = $koneksi->prepare("SELECT * FROM vehicle_photos WHERE vehicle_id = ? AND deleted_at IS NULL");
$getdataPhoto->execute([$id]);
$vehiclePhoto_id = $getdataPhoto->fetch(PDO::FETCH_ASSOC);

if (!$vehicle) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Vehicle tidak ditemukan.");
}

$branches = $koneksi->query("SELECT * FROM branches WHERE deleted_at IS NULL")->fetchAll();

$vehicleModels = $koneksi->query("SELECT vehicle_models.id, vehicle_models.name AS model_name, brands.name AS brand_name FROM vehicle_models JOIN brands ON vehicle_models.brand_id = brands.id WHERE vehicle_models.deleted_at IS NULL")->fetchAll();

$stnk_deadline = new DateTime($vehicle['stnk_deadline']);
$today = new DateTime();
$interval = $today->diff($stnk_deadline);
$sisaHari = $interval->days . " hari (" . ($stnk_deadline > $today ? "tersisa" : "lewat") . ")";

// Siapkan array untuk dropdown jenis kendaraan.
$types = [
    'motorcycle' => 'Motor',
    'car' => 'Mobil'
];

$typefuels = [
    'gasoline' => 'Bensin',
    'electric' => 'Listrik',
    'hybrid' => 'Hybrid'
];

function getVehicleIcon($type)
{
    return [
        'car' => 'mdi mdi-car-hatchback',
        'motorcycle' => 'mdi mdi-motorbike'
    ][$type] ?? 'mdi mdi-car';
}

function getFuelIcon($fuel)
{
    return [
        'electric' => 'mdi mdi-lightning-bolt',
        'gasoline' => 'mdi mdi-fuel',
        'hybrid' => 'mdi mdi-cached'
    ][$fuel] ?? 'mdi mdi-alert-circle';
}

function formatIndoDate($dateStr)
{
    $formatter = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN,
        "EEEE, dd MMMM yyyy"
    );
    $timestamp = strtotime($dateStr);
    return $formatter->format($timestamp);
}

// Siapkan array untuk dropdown status kendaraan.
$statuses = [
    'available' => 'Tersedia',
    'on_loan' => 'Dipinjam',
    'test_drive' => 'Tes Jalan',
    'transaction' => 'Dalam Transaksi',
    'service' => 'Service',
    'sold' => 'Terjual',
];

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<style>
    @keyframes slideDown {
        0% {
            transform: translateY(-100px);
            opacity: 0;
        }

        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        transform: translateY(-100px);
        opacity: 0;
    }

    .modal.fade.show .modal-dialog {
        transform: translateY(0);
        opacity: 1;
        animation: slideDown 0.3s ease-out;
    }

    .fullscreen-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.9);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .fullscreen-overlay img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        cursor: zoom-out;
    }
</style>

<div id="floating-alert-container" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999; max-width: 300px;"></div>

<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Kendaraan</h3>

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
                            <th class="w-25">Tahun Produksi</th>
                            <td><?= formatIndoDate($vehicle['production_year']) ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Jatuh Tempo STNK</th>
                            <td><?= formatIndoDate($vehicle['stnk_deadline']) ?> <br><small class="text-muted"><?= $sisaHari ?></small></td>
                        </tr>
                        <tr>
                            <th class="w-25">Bahan Bakar</th>
                            <td><?= $typefuels[$vehicle['type_fuel']] ?? '-' ?> <i class="<?= getFuelIcon($vehicle['type_fuel']) ?>"></i></td>
                        </tr>
                        <tr>
                            <th class="w-25">CC Mesin</th>
                            <td><?= htmlspecialchars($vehicle['cc_engine']) ?> cc</td>
                        </tr>
                        <tr>
                            <th class="w-25">Serial Number</th>
                            <td><?= htmlspecialchars($vehicle['serial_number']) ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Kilometer</th>
                            <td><?= htmlspecialchars($vehicle['kilometer']) ?> KM</td>
                        </tr>
                        <tr>
                            <th class="w-25">Harga Terendah</th>
                            <td>Rp <?= number_format($vehicle['lowest_price'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Harga Display</th>
                            <td>Rp <?= number_format($vehicle['price_displayed'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Status</th>
                            <td><?= $statuses[$vehicle['status']] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th class="w-25">Deskripsi</th>
                            <td class="text-wrap"><?= htmlspecialchars($vehicle['description']) ?></td>
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
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Warna</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($vehicle['color']) ?></div>
                    </div>

                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Tahun Produksi</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= formatIndoDate($vehicle['production_year']) ?></div>
                    </div>

                    <hr class="my-3">
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-3"><strong>Jatuh Tempo STNK</strong></div>
                        <div class="col-12 col-md-9 text-small"><?= formatIndoDate($vehicle['stnk_deadline']) ?> <br><small class="text-muted"><?= $sisaHari ?></small></div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Bahan Bakar</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= $typefuels[$vehicle['type_fuel']] ?? '-' ?> <i class="<?= getFuelIcon($vehicle['type_fuel']) ?>"></i></div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>CC Mesin</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($vehicle['cc_engine']) ?> cc</div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Nomor Seri Kendaraan</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($vehicle['serial_number']) ?></div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Kilometer</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($vehicle['kilometer']) ?> km</div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Harga Terendah</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap">Rp <?= number_format($vehicle['lowest_price'], 0, ',', '.') ?></div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Harga Display</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap">Rp <?= number_format($vehicle['price_displayed'], 0, ',', '.') ?></div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Status</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= $statuses[$vehicle['status']]  ?></div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3"><strong>Deskripsi</strong></div>
                        <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($vehicle['description']) ?></div>
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

        <!-- Table Soft Delete Vehicle Documents -->
        <div class="mt-5">

            <h3 class="mb-4">Data Dokumen Kendaraan</h3>

            <!-- Ubah agar tombol bisa dikontrol dari JS -->
            <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
                <button type="button" id="btn-add-document" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadDocument" style="display: none;">
                    Tambah
                </button>
            </div>

            <?php
            $activePage = basename($_SERVER['PHP_SELF']);
            ?>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link text-primary <?= ($activePage == 'detail.php' || $activePage == 'delete-vehicle-photos.php') ? 'active' : '' ?>" href="detail.php?id=<?= $vehicle['id'] ?>">Data Aktif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary <?= ($activePage == 'delete-vehicle-documents.php') ? 'active' : '' ?>" href="delete-vehicle-documents.php?id=<?= $vehicle['id'] ?>">Data Terhapus</a>
                </li>
            </ul>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body overflow-auto">
                            <table class="table table-striped" id="productTable">
                                <thead>
                                    <tr>
                                        <th>STNK</th>
                                        <th>BPKB</th>
                                        <th>Nota Service</th>
                                        <th>Nota Pembelian</th>
                                        <th>Asuransi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="vehicleDocumentTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Table Vehicle Photos -->
        <div class="mt-5">
            <h3 class="mb-4">Data Foto Kendaraan</h3>

            <?php
            $photoCountStmt = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_photos WHERE vehicle_id = ? AND (deleted_at IS NULL AND deleted_by_vehicle_at IS NULL)");
            $photoCountStmt->execute([$id]);
            $photoCount = $photoCountStmt->fetchColumn();

            $coverCheckStmt = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_photos WHERE vehicle_id = ? AND is_cover = 1 AND (deleted_at IS NULL AND deleted_by_vehicle_at IS NULL)");
            $coverCheckStmt->execute([$id]);
            $hasCover = $coverCheckStmt->fetchColumn() > 0;

            $query = $koneksi->prepare("SELECT * FROM vehicle_photos WHERE vehicle_id = :vehicle_id AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL ORDER BY created_at ASC");
            $query->bindValue(':vehicle_id', $id, PDO::PARAM_STR);
            $query->execute();
            $dataVehiclePhotos = $query->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <!-- Tombol Memunculkan Form Tambah Data Vehicle Documents -->
            <?php if ($photoCount < 6): ?>
                <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadPhoto">
                        Tambah
                    </button>
                </div>
            <?php endif ?>

            <?php
            $activePage = basename($_SERVER['PHP_SELF']);
            ?>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link text-primary <?= ($activePage == 'detail.php') ? 'active' : '' ?>" href="detail.php?id=<?= $vehicle['id'] ?>">Data Aktif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary <?= ($activePage == 'delete-vehicle-photos.php') ? 'active' : '' ?>" href="delete-vehicle-photos.php?id=<?= $vehicle['id'] ?>">Data Terhapus</a>
                </li>
            </ul>


            <div class="row">
                <?php
                $query = $koneksi->prepare("SELECT * FROM vehicle_photos WHERE vehicle_id = :vehicle_id AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL) ORDER BY created_at ASC");
                $query->bindValue(':vehicle_id', $id, PDO::PARAM_STR);
                $query->execute();

                $dataVehiclePhotos = $query->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($dataVehiclePhotos as $row): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img
                                src="../../../../storage/<?= $row['photo_path'] ?>"
                                class="card-img-top img-thumbnail fullscreen-img"
                                alt="Foto Kendaraan"
                                onclick="openFullscreen(this)"
                                style="height: 200px; object-fit: cover; cursor: zoom-in;">
                            <div class="card-body d-flex justify-content-center gap-2">
                                <?php if ($photoCount < 6): ?>
                                    <a href="./vehicle_photos/restore.php?id=<?= $row['id'] ?>&vehicle_id=<?= $vehicle['id'] ?>" title='Restore' class='btn btn-success btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white;'>
                                        <i class='mdi mdi-restore'></i>
                                    </a>
                                <?php endif; ?>
                                <a href="./vehicle_photos/destroy.php?id=<?= $row['id'] ?>&vehicle_id=<?= $vehicle['id'] ?>" title='Permanent Delete' class='btn btn-danger btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white;'>
                                    <i class='mdi mdi-delete-forever' style='color: white;'></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($dataVehiclePhotos)): ?>
                    <div class="col-12 text-center">
                        <p>Tidak ada data kendaraan yang terhapus.</p>
                    </div>
                <?php endif; ?>
            </div>


        </div>



        <?php
        // Mengimpor bagian layout footer (termasuk tag penutup, script JS, dll).
        include '../layout/footer.php';
        ?>
    </div>
</div>

<!-- Modal Tambah Documents -->
<div class="modal fade" id="modalUploadDocument" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="./vehicle_documents/create.php" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Tambah Dokumen Kendaraan '<?= $vehicle['id'] ?>'</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>">
                <div class="mb-3">
                    <label for="stnk" class="form-label">STNK</label>
                    <input type="file" name="stnk" accept="image/*,.pdf" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="bpkb" class="form-label">BPKB</label>
                    <input type="file" name="bpkb" accept="image/*,.pdf" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="service_note" class="form-label">Nota Service</label>
                    <input type="file" name="service_note" accept="image/*,.pdf" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="nota" class="form-label">Nota Pembelian</label>
                    <input type="file" name="nota" accept="image/*,.pdf" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="asuransi" class="form-label">Asuransi</label>
                    <input type="file" name="asuransi" accept="image/*,.pdf" class="form-control">
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Documents -->
<div class="modal fade" id="modalEditDocument" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="./vehicle_documents/edit.php?id=<?= $vehicle_id['id'] ?>" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Edit Dokumen Kendaraan '<?= $vehicle['id'] ?>'</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>">
                <div class="mb-3">
                    <label for="stnk" class="form-label">STNK</label>
                    <input type="file" name="stnk" accept="image/*,.pdf" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="bpkb" class="form-label">BPKB</label>
                    <input type="file" name="bpkb" accept="image/*,.pdf" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="service_note" class="form-label">Nota Service</label>
                    <input type="file" name="service_note" accept="image/*,.pdf" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="nota" class="form-label">Nota Pembelian</label>
                    <input type="file" name="nota" accept="image/*,.pdf" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="asuransi" class="form-label">Asuransi</label>
                    <input type="file" name="asuransi" accept="image/*,.pdf" class="form-control">
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Photos -->
<div class="modal fade" id="modalUploadPhoto" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="./vehicle_photos/create.php" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Tambah Foto Kendaraan '<?= $vehicle['id'] ?>'</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>">
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" name="photo_path" accept="image/*" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<div id="fullscreenOverlay" class="fullscreen-overlay" onclick="closeFullscreen()">
    <img id="fullscreenImage" src="">
</div>

<script>
    function openFullscreen(imgElement) {
        document.getElementById('fullscreenImage').src = imgElement.src;
        document.getElementById('fullscreenOverlay').style.display = 'flex';
    }

    function closeFullscreen() {
        document.getElementById('fullscreenOverlay').style.display = 'none';
        document.getElementById('fullscreenImage').src = '';
    }
</script>

<script>
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('vehicleDocumentTableBody');

    // Ambil vehicle_id dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const vehicleId = urlParams.get('id');


    function loadVehicleDocuments(keyword = '') {
        fetch(`vehicle_documents/ajaxVehicleDocumentList.php?vehicle_id=${vehicleId}`)
            .then(res => res.text())
            .then(html => {
                tableBody.innerHTML = html;
                bindDeleteEvents(); // wajib ulang
                checkAddButtonVisibility(); // ← tambahkan di sini
            });
    }

    function checkAddButtonVisibility() {
        fetch(`vehicle_documents/checkActiveDocument.php?vehicle_id=${vehicleId}`)
            .then(res => res.json())
            .then(data => {
                const addButton = document.getElementById('btn-add-document');
                if (addButton) {
                    if (data.active) {
                        addButton.style.display = 'none'; // Ada data aktif → sembunyikan
                    } else {
                        addButton.style.display = 'inline-block'; // Tidak ada → tampilkan
                    }
                }
            });
    }

    // Load pertama
    loadVehicleDocuments();

    // Saat ketik di search
    searchInput.addEventListener('keyup', function() {
        loadVehicleDocuments(this.value);
    });
</script>

<script>
    function bindDeleteEvents() {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const vehicleId = this.dataset.vehicleId;

                fetch(`vehicle_documents/softDelete.php?id=${id}&vehicle_id=${vehicleId}`)
                    .then(() => {
                        loadVehicleDocuments(); // reload tabel
                        showAlert(`Dokumen untuk kendaraan <strong>${vehicleId}</strong> berhasil dihapus sementara.`, 'danger');
                    });
            });
        });
    }

    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} shadow rounded mb-2 fade-out`;

        // Masukkan isi alert + tombol close
        alertDiv.innerHTML = `<span>${message}</span>`;
        alertDiv;

        const container = document.getElementById('floating-alert-container');
        container.appendChild(alertDiv);

        // Fade out mulai di detik ke-4.5
        setTimeout(() => {
            alertDiv.style.opacity = '0';
        }, 1500);

        // Remove dari DOM di detik ke-5
        setTimeout(() => {
            alertDiv.remove();
        }, 2000);
    }

    function loadVehicleDocuments(keyword = '') {
        fetch(`vehicle_documents/ajaxVehicleDocumentList.php?vehicle_id=${vehicleId}`)
            .then(res => res.text())
            .then(html => {
                tableBody.innerHTML = html;
                bindDeleteEvents(); // PENTING!
                checkAddButtonVisibility(); // ← tambahkan di sini
            });
    }

    // Event awal
    document.addEventListener('DOMContentLoaded', () => {
        loadVehicleDocuments();

        searchInput.addEventListener('keyup', function() {
            loadVehicleDocuments(this.value);
        });
    });
</script>
<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlert(`<?= $_SESSION['success_message'] ?>`, 'success');
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['danger_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlert(`<?= $_SESSION['danger_message'] ?>`, 'danger');
        });
    </script>
    <?php unset($_SESSION['danger_message']); ?>
<?php endif; ?>