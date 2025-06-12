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
$data = $koneksi->prepare("SELECT * FROM vehicles WHERE id = ?");
$data->execute([$id]);
$vehicle = $data->fetch(PDO::FETCH_ASSOC);

if (!$vehicle) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Vehicle tidak ditemukan.");
}

$branches = $koneksi->query("SELECT * FROM branches WHERE deleted_at IS NULL")->fetchAll();

$vehicleModels = $koneksi->query("SELECT vehicle_models.id, vehicle_models.name AS model_name, brands.name AS brand_name FROM vehicle_models JOIN brands ON vehicle_models.brand_id = brands.id WHERE vehicle_models.deleted_at IS NULL")->fetchAll();

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
    'available' => 'Tersedia',
    'service' => 'Service',
    'test_drive' => 'Tes Jalan',
    'sold' => 'Terjual'
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

        <?php
        // Menjalankan fungsi untuk menampilkan alert jika ada.
        showAlert();
        ?>

        <!-- Detail Vehicle -->
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="kode_kendaraan" class="form-label">Kode Kendaraan</label>
                        <input type="text" class="form-control" id="id_show" name="id_show" value="<?= $vehicle['id'] ?>" disabled>
                        <input type="hidden" class="form-control" id="id" name="id" value="<?= $vehicle['id'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="vehicle_model_id" class="form-label">Model Kendaraan</label>
                        <select class="form-select" id="vehicle_model_id" name="vehicle_model_id" style="color: black;" disabled>
                            <?php foreach ($vehicleModels as $model): ?>
                                <option value="<?= $model['id'] ?>" <?= $vehicle['vehicle_model_id'] == $model['id'] ? 'selected' : '' ?>>
                                    <?= $model['brand_name'] . ' - ' . $model['model_name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                        <select class="form-select" id="type_vehicle" name="type_vehicle" style="color: black;" disabled>
                            <?php foreach ($types as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $vehicle['type_vehicle'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach ?>
                        </select>

                    </div>

                    <div class="mb-3">
                        <label for="bahan_bakar" class="form-label">Bahan Bakar</label>
                        <select class="form-select" id="type_fuel" name="type_fuel" style="color: black;" disabled>
                            <?php foreach ($typefuels as $valuefuel => $labelfuel): ?>
                                <option value="<?= $valuefuel ?>" <?= ($vehicle['type_fuel'] ?? '') == $valuefuel ? 'selected' : '' ?>><?= $labelfuel ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="warna" class="form-label">Warna</label>
                        <input type="text" class="form-control" id="color" name="color" value="<?= $vehicle['color'] ?>" disabled>
                    </div>


                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tahun_produksi" class="form-label">Tahun Produksi</label>
                            <input type="date" class="form-control" id="production_year" name="production_year" value="<?= $vehicle['production_year'] ?>" disabled>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nomor_mesin" class="form-label">Nomor Mesin</label>
                            <input type="number" class="form-control" id="serial_number" name="serial_number" value="<?= $vehicle['serial_number'] ?>" disabled>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tenggat_SNTK" class="form-label">Tenggat STNK</label>
                            <input type="date" class="form-control" id="stnk_deadline" name="stnk_deadline" value="<?= $vehicle['stnk_deadline'] ?>" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kilometer" class="form-label">Kilometer</label>
                            <input type="number" class="form-control" id="kilometer" name="kilometer" value="<?= $vehicle['kilometer'] ?>" disabled>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cc_mesin" class="form-label">CC Mesin</label>
                            <input type="number" class="form-control" id="cc_engine" name="cc_engine" value="<?= $vehicle['cc_engine'] ?>" disabled>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?= $vehicle['price'] ?>" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" style="color: black;" disabled>
                                <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $vehicle['status'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cabang" class="form-label">Cabang</label>
                            <select class="form-select" id="branch_id" name="branch_id" style="color: black;" disabled>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= $vehicle['branch_id'] == $branch['id'] ? 'selected' : '' ?>><?= $branch['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="Deskripsi" class="form-label">Deskripsi</label>
                        <textarea cols="15" rows="15" type="alamat" class="form-control" id="description" name="description" placeholder="Masukan Deskripsi" disabled><?= $vehicle['description'] ?></textarea>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Soft Delete Vehicle Documents -->
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
                                    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                                    $limit = 10;
                                    $offset = ($page - 1) * $limit;

                                    $stmtCount = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
                                    $stmtCount->bindValue(':vehicle_id', $id, PDO::PARAM_STR);
                                    $stmtCount->execute();
                                    $totalData = $stmtCount->fetchColumn();
                                    var_dump($totalData);
                                    $totalPages = ceil($totalData / $limit);

                                    $query = $koneksi->prepare("SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL) ORDER BY created_at ASC LIMIT :limit OFFSET :offset");
                                    $query->bindValue(':vehicle_id', $id, PDO::PARAM_STR);
                                    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
                                    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
                                    $query->execute();

                                    $dataVehicleDocuments = $query->fetchAll(PDO::FETCH_ASSOC);

                                    $formatTeks = function ($filePath, $label) {
                                        if (empty($filePath)) return 'Kosong';
                                        return "<a href='../../../../storage/" . $filePath . "' target='_blank'>Buka $label</a>";
                                    };

                                    $cekAktif = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = ? AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL");
                                    $cekAktif->execute([$id]);
                                    $adaAktif = $cekAktif->fetchColumn() > 0;

                                    foreach ($dataVehicleDocuments as $row) {
                                        echo "<tr>
                                            <td>" . $formatTeks($row['stnk'], 'STNK') . "</td>
                                            <td>" . $formatTeks($row['bpkb'], 'BPKB') . "</td>
                                            <td>" . $formatTeks($row['service_note'], 'Nota Service') . "</td>
                                            <td>" . $formatTeks($row['nota'], 'Nota') . "</td>
                                            <td>" . $formatTeks($row['asuransi'], 'Asuransi') . "</td>
                                            <td style='display: flex; align-items: center; gap: 8px;'>";
                                        if (!$adaAktif) {
                                            echo "<a href='./vehicle_documents/restore.php?id={$row['id']}&vehicle_id={$vehicle['id']}' title='Restore' class='btn btn-success btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white;'>
                                                    <i class='mdi mdi-restore'></i>
                                                </a>";
                                        }

                                        echo "<a href='./vehicle_documents/destroy.php?id={$row['id']}&vehicle_id={$vehicle['id']}' title='Permanent Delete' class='btn btn-danger btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white;'>
                                                    <i class='mdi mdi-delete-forever' style='color: white;'></i>
                                                </a>
                                            </td>
                                        </tr>";
                                    }
                                    if (empty($dataVehicleDocuments)) {
                                        echo "<tr><td colspan='8' class='text-center'>Dokumen kendaraan {$id} yang di hapus kosong.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($totalPages > 1) : ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link bg-primary text-white" href="?page=<?= $page - 1 ?>&id=<?= $id ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link <?= ($page == $i) ? 'bg-primary text-white' : 'text-primary bg-white' ?>" href="?page=<?= $i ?>&id=<?= $id ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link bg-primary text-white" href="?page=<?= $page + 1 ?>&id=<?= $id ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <!-- Table Vehicle Photos -->
        <div class="mt-5">
            <h3 class="mb-4">Data Foto Kendaraan</h3>

            <?php
            $photoCountStmt = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_photos WHERE vehicle_id = ? AND (deleted_at IS NULL AND deleted_by_vehicle_at IS NULL)");
            $photoCountStmt->execute([$id]);
            $photoCount = $photoCountStmt->fetchColumn();
            var_dump($photoCount);

            $query = $koneksi->prepare("SELECT * FROM vehicle_photos WHERE vehicle_id = :vehicle_id AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL ORDER BY created_at ASC");
            $query->bindValue(':vehicle_id', $id, PDO::PARAM_STR);
            $query->execute();
            $dataVehiclePhotos = $query->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if ($photoCount < 5): ?>
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalUploadPhoto">Tambah</button>
            <?php endif; ?>

            <?php
            $activePage = basename($_SERVER['PHP_SELF']);
            ?>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link text-primary <?= ($activePage == 'detail.php' || $activePage == 'delete-vehicle-documents.php') ? 'active' : '' ?>" href="detail.php?id=<?= $vehicle['id'] ?>">Data Aktif</a>
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
                            <div class="card-body d-flex justify-content-center gap-2">
                                <button data-bs-toggle="modal" data-bs-target="#modalEditPhoto" title="Edit"
                                    class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <a href="./vehicle_photos/softDelete.php?id=<?= $row['id'] ?>&vehicle_id=<?= $vehicle['id'] ?>"
                                    title="Delete" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: white;">
                                    <i class="mdi mdi-delete-restore"></i>
                                </a>
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