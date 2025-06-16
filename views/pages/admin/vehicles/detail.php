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

// Siapkan array untuk dropdown status kendaraan.
$statuses = [
    'on_loan' => 'Dipinjam',
    'service' => 'Service',
    'sold' => 'Terjual',
    'available' => 'Tersedia',
    'test_drive' => 'Tes Jalan',
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


<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Kendaraan</h3>

        <!-- TABEL Brand & Model -->
        <div class="card mb-4">
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

        <!-- TABEL KENDARAAN -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Informasi Kendaraan</h5>
                <table class="table">
                    <tr>
                        <th class="w-25">Kode</th>
                        <td><?= htmlspecialchars($vehicle['id']) ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Tipe</th>
                        <td><?= $types[$vehicle['type_vehicle']] ?? '-' ?></td>
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
                        <td><?= $typefuels[$vehicle['type_fuel']] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">CC Engine</th>
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
                        <td>Rp <?= htmlspecialchars($vehicle['lowest_price']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Harga Display</th>
                        <td>Rp <?= htmlspecialchars($vehicle['price_displayed']) ?></td>
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

        <!-- TABEL Cabang -->
        <div class="card mb-4">
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

        <!-- Table Vehicle Documents -->
        <div class="mt-5">
            <h3 class="mb-4">Data Dokumen Kendaraan</h3>

            <?php
            $cekVehicleDocs = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = ? AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL");
            $cekVehicleDocs->execute([$id]);
            $adaDokumenAktif = $cekVehicleDocs->fetchColumn();

            $cekDeletedVehicleDocs = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
            $cekDeletedVehicleDocs->execute([$id]);
            $adaDokumenSoftDelete = $cekDeletedVehicleDocs->fetchColumn();

            $bolehTambahDokumen = ($adaDokumenAktif == 0 && $adaDokumenSoftDelete >= 0);
            ?>

            <!-- Tombol Memunculkan Form Tambah Data Vehicle Documents -->
            <?php if ($bolehTambahDokumen): ?>
                <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadDocument">
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
                    <a class="nav-link text-primary <?= ($activePage == 'delete-vehicle-documents.php') ? 'active' : '' ?>" href="delete-vehicle-documents.php?id=<?= $vehicle['id'] ?>">Data Terhapus</a>
                </li>
            </ul>



            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
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
                                <tbody>
                                    <?php
                                    $query = $koneksi->prepare("SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL ORDER BY created_at ASC");
                                    $query->bindValue(':vehicle_id', $id, PDO::PARAM_STR);
                                    $query->execute();

                                    $dataVehicleDocuments = $query->fetchAll(PDO::FETCH_ASSOC);

                                    $formatTeks = function ($filePath, $label) {
                                        if (empty($filePath)) return 'Kosong';
                                        return "<a href='../../../../storage/" . $filePath . "' target='_blank'>Buka $label</a>";
                                    };

                                    foreach ($dataVehicleDocuments as $row) {
                                        echo "<tr>
                                            <td>" . $formatTeks($row['stnk'], 'STNK') . "</td>
                                            <td>" . $formatTeks($row['bpkb'], 'BPKB') . "</td>
                                            <td>" . $formatTeks($row['service_note'], 'Nota Service') . "</td>
                                            <td>" . $formatTeks($row['nota'], 'Nota') . "</td>
                                            <td>" . $formatTeks($row['asuransi'], 'Asuransi') . "</td>
                                            <td style='display: flex; align-items: center; gap: 8px;'>
                                                <button  data-bs-toggle='modal' data-bs-target='#modalEditDocument' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                                                    <i class='mdi mdi-pencil'></i>
                                                </button>
                                                <a href='./vehicle_documents/softDelete.php?id={$row['id']}&vehicle_id={$vehicle['id']}' title='Delete' class='btn btn-danger btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; color: white; border-radius: 4px;'>
                                                    <i class='mdi mdi-delete-restore'></i>
                                                </a>
                                            </td>
                                        </tr>";
                                    }
                                    if (empty($dataVehicleDocuments)) {
                                        echo "<tr><td colspan='8' class='text-center'>Dokumen kendaraan {$id} belum ditambahkan.</td></tr>";
                                    }
                                    ?>
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

            <?php if ($photoCount < 6): ?>
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalUploadPhoto">Tambah</button>
            <?php endif; ?>

            <?php $activePage = basename($_SERVER['PHP_SELF']); ?>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link text-primary <?= ($activePage == 'detail.php') ? 'active' : '' ?>" href="detail.php?id=<?= $vehicle['id'] ?>">Data Aktif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary <?= ($activePage == 'delete-vehicle-photos.php') ? 'active' : '' ?>" href="delete-vehicle-photos.php?id=<?= $vehicle['id'] ?>">Data Terhapus</a>
                </li>
            </ul>

            <div class="row">
                <?php foreach ($dataVehiclePhotos as $row): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img
                                src="../../../../storage/<?= $row['photo_path'] ?>"
                                class="card-img-top img-thumbnail fullscreen-img"
                                alt="Foto Kendaraan"
                                onclick="openFullscreen(this)"
                                style="height: 200px; object-fit: cover; cursor: zoom-in;">
                            <div class="card-body d-flex flex-column align-items-center gap-2">
                                <?php if ($row['is_cover']): ?>
                                    <span class="badge bg-success mb-2">Foto Cover</span>
                                <?php endif; ?>

                                <div class="d-flex gap-2">
                                    <button data-bs-toggle="modal" data-bs-target="#modalEditPhoto" title="Edit"
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <a href="./vehicle_photos/softDelete.php?id=<?= $row['id'] ?>&vehicle_id=<?= $vehicle['id'] ?>"
                                        title="Delete" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: white;">
                                        <i class="mdi mdi-delete-restore"></i>
                                    </a>
                                </div>

                                <?php if ($row['is_cover'] || !$hasCover): ?>
                                    <form action="vehicle_photos/setCover.php" method="POST">
                                        <input type="hidden" name="photo_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>">
                                        <button type="submit" class="btn btn-outline-<?= $row['is_cover'] ? 'danger' : 'success' ?> btn-sm mt-2">
                                            <?= $row['is_cover'] ? 'Lepas Cover' : 'Jadikan Cover' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($dataVehiclePhotos)): ?>
                    <div class="col-12 text-center">
                        <p>Tidak ada data foto kendaraan yang aktif.</p>
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

<!-- Modal Edit Photos -->
<div class="modal fade" id="modalEditPhoto" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="./vehicle_photos/edit.php?id=<?= $vehiclePhoto_id['id'] ?>" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Edit Foto Kendaraan '<?= $vehicle['id'] ?>'</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>">
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" name="photo_path" accept="image/*" class="form-control">
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