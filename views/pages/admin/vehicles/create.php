<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

// 🔎 Melakukan Pengecekan Apakah Sudah Login atau Belum
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $id = $_POST['id'];
    $vehicle_model_id = $_POST['vehicle_model_id'];
    $type = $_POST['type_vehicle'];
    $type_fuel = $_POST['type_fuel'];
    $color = $_POST['color'];
    $production = $_POST['production_year'];
    $serial = $_POST['serial_number'];
    $stnk = $_POST['stnk_deadline'];
    $kilometer = $_POST['kilometer'];
    $cc = $_POST['cc_engine'];
    $desc = $_POST['description'];
    $lowest_price = $_POST['lowest_price'];
    $price_displayed = $_POST['price_displayed'];
    $status = $_POST['status'];
    $user = $_SESSION['user_id'];
    $branch = $_POST['branch_id'];

    // ⬇️ Simpan data ke database (id, brand_model, type_vehicle, color, production_year, serial_number, stnk_deadline, kilometer, cc_engine, description, price, status, user_id, branch_id, created_at)
    $data = $koneksi->prepare("INSERT INTO vehicles (id, vehicle_model_id, type_vehicle, color, production_year, serial_number, stnk_deadline, type_fuel, kilometer, cc_engine, description, lowest_price, price_displayed, status, user_id, branch_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $data->execute([$id, $vehicle_model_id, $type, $color, $production, $serial, $stnk, $type_fuel, $kilometer, $cc, $desc, $lowest_price, $price_displayed, $status, $user, $branch]);


    $_SESSION['success_message'] = "Kendaraan <strong>" . htmlspecialchars($id) . "</strong> berhasil ditambahkan.";

    // 🚀 Setelah berhasil, balik ke halaman index
    header("Location: vehicles.php");
    exit;
}

// 🔍 Ambil hanya cabang yang belum dihapus (soft delete = null)
$branches = $koneksi->query("SELECT * FROM branches WHERE deleted_at IS NULL")->fetchAll();

$vehicleModels = $koneksi->query("SELECT vehicle_models.id, vehicle_models.name AS model_name, brands.name AS brand_name FROM vehicle_models JOIN brands ON vehicle_models.brand_id = brands.id WHERE vehicle_models.deleted_at IS NULL")->fetchAll();

$types = [
    'motorcycle' => 'Motor',
    'car' => 'Mobil'
];

$typefuels = [
    'gasoline' => 'Bensin',
    'electric' => 'Listrik',
    'hybrid' => 'Hybrid'
];

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


<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Kendaraan</h3>

        <!-- Card Wrapper -->
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="kode_kendaraan" class="form-label">Kode Kendaraan<span class="mx-1 text-danger">*</span></label>
                        <input type="text" class="form-control" id="id" name="id" placeholder="Masukan Kode Kendaraan">
                    </div>

                    <div class="mb-3">
                        <label for="vehicle_model_id" class="form-label">Model Kendaraan<span class="mx-1 text-danger">*</span></label>
                        <select class="form-select" id="vehicle_model_id" name="vehicle_model_id" style="color: black;">
                            <?php foreach ($vehicleModels as $model): ?>
                                <option value="<?= $model['id'] ?>">
                                    <?= $model['brand_name'] . ' - ' . $model['model_name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan<span class="mx-1 text-danger">*</span></label>
                        <select class="form-select" id="type_vehicle" name="type_vehicle" style="color: black;">
                            <?php foreach ($types as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($data['type_vehicle'] ?? '') == $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="bahan_bakar" class="form-label">Bahan Bakar<span class="mx-1 text-danger">*</span></label>
                        <select class="form-select" id="type_fuel" name="type_fuel" style="color: black;">
                            <?php foreach ($typefuels as $valuefuel => $labelfuel): ?>
                                <option value="<?= $valuefuel ?>" <?= ($data['type_fuel'] ?? '') == $valuefuel ? 'selected' : '' ?>><?= $labelfuel ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="warna" class="form-label">Warna<span class="mx-1 text-danger">*</span></label>
                        <input type="text" class="form-control" id="color" name="color" placeholder="Masukan Warna Kendaraan">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nomor_mesin" class="form-label">Nomor Mesin<span class="mx-1 text-danger">*</span></label>
                            <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Masukan Nomor Mesin">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kilometer" class="form-label">Kilometer<span class="mx-1 text-danger">*</span></label>
                            <input type="number" class="form-control" id="kilometer" name="kilometer" placeholder="Masukan Kilometer Kendaraan">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cc_mesin" class="form-label">CC Mesin<span class="mx-1 text-danger">*</span></label>
                            <input type="number" class="form-control" id="cc_engine" name="cc_engine" placeholder="Masukan CC Mesin Kendaraan">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tahun_produksi" class="form-label">Tahun Produksi<span class="mx-1 text-danger">*</span></label>
                            <input type="date" class="form-control" id="production_year" name="production_year" placeholder="Masukan Tahun Produksi">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tenggat_SNTK" class="form-label">Tenggat STNK<span class="mx-1 text-danger">*</span></label>
                            <input type="date" class="form-control" id="stnk_deadline" name="stnk_deadline" placeholder="Masukan Tenggat STNK">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label">Harga Terendah<span class="mx-1 text-danger">*</span></label>
                            <input type="number" class="form-control" id="lowest_price" name="lowest_price" placeholder="Masukan Harga Kendaraan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label">Harga Display<span class="mx-1 text-danger">*</span></label>
                            <input type="number" class="form-control" id="price_displayed" name="price_displayed" placeholder="Masukan Harga Kendaraan">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status<span class="mx-1 text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" style="color: black;">
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($data['status'] ?? '') == $value ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cabang" class="form-label">Cabang<span class="mx-1 text-danger">*</span></label>
                            <select class="form-select" id="branch_id" name="branch_id" style="color: black;">
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= ($data['branch_id'] ?? '') == $branch['id'] ? 'selected' : '' ?>><?= $branch['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="Deskripsi" class="form-label">Deskripsi<span class="mx-1 text-danger">*</span></label>
                        <textarea cols="15" rows="15" type="alamat" class="form-control" id="description" name="description" placeholder="Masukan Deskripsi"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="vehicles.php" class="btn btn-secondary text-white mx-2" style="color: white; margin-left: 15px;">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>