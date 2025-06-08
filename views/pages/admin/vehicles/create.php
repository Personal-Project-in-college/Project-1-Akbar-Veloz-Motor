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
    $brand = $_POST['brand_model'];
    $type = $_POST['type_vehicle'];
    $color = $_POST['color'];
    $production = $_POST['production_year'];
    $serial = $_POST['serial_number'];
    $stnk = $_POST['stnk_deadline'];
    $kilometer = $_POST['kilometer'];
    $cc = $_POST['cc_engine'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $user = $_SESSION['user_id'];
    $branch = $_POST['branch_id'];

    // ⬇️ Simpan data ke database (id, brand_model, type_vehicle, color, production_year, serial_number, stnk_deadline, kilometer, cc_engine, description, price, status, user_id, branch_id, created_at)
    $data = $koneksi->prepare("INSERT INTO vehicles (id, brand_model, type_vehicle, color, production_year, serial_number, stnk_deadline, kilometer, cc_engine, description, price, status, user_id, branch_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $data->execute([$id, $brand, $type, $color, $production, $serial, $stnk, $kilometer, $cc, $desc, $price, $status, $user, $branch]);


    $_SESSION['success'] = "Kendaraan <strong>" . htmlspecialchars($id) . "</strong> berhasil ditambahkan.";

    // 🚀 Setelah berhasil, balik ke halaman index
    header("Location: vehicles.php");
    exit;
}

// 🔍 Ambil hanya cabang yang belum dihapus (soft delete = null)
$branches = $koneksi->query("SELECT * FROM branches WHERE deleted_at IS NULL")->fetchAll();

$types = [
    'motorcycle' => 'Motor',
    'car' => 'Mobil'
];

$statuses = [
    'available' => 'Tersedia',
    'service' => 'Service',
    'test_drive' => 'Tes Jalan',
    'sold' => 'Terjual'
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
                        <label for="kode_kendaraan" class="form-label">Kode Kendaraan</label>
                        <input type="text" class="form-control" id="id" name="id" placeholder="Masukan Kode Kendaraan">
                    </div>

                    <div class="mb-3">
                        <label for="brand" class="form-label">Brand</label>
                        <input type="text" class="form-control" id="brand_model" name="brand_model" placeholder="Masukan Brand Kendaraan">
                    </div>

                    <div class="mb-3">
                        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                        <select class="form-select" id="type_vehicle" name="type_vehicle" style="color: black;">
                            <?php foreach ($types as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($data['type_vehicle'] ?? '') == $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="warna" class="form-label">Warna</label>
                        <input type="text" class="form-control" id="color" name="color" placeholder="Masukan Warna Kendaraan">
                    </div>


                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tahun_produksi" class="form-label">Tahun Produksi</label>
                            <input type="date" class="form-control" id="production_year" name="production_year" placeholder="Masukan Tahun Produksi">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nomor_mesin" class="form-label">Nomor Mesin</label>
                            <input type="number" class="form-control" id="serial_number" name="serial_number" placeholder="Masukan Nomor Mesin">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tenggat_SNTK" class="form-label">Tenggat STNK</label>
                            <input type="date" class="form-control" id="stnk_deadline" name="stnk_deadline" placeholder="Masukan Tenggat STNK">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kilometer" class="form-label">Kilometer</label>
                            <input type="number" class="form-control" id="kilometer" name="kilometer" placeholder="Masukan Kilometer Kendaraan">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cc_mesin" class="form-label">CC Mesin</label>
                            <input type="number" class="form-control" id="cc_engine" name="cc_engine" placeholder="Masukan CC Mesin Kendaraan">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="Masukan Harga Kendaraan">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" style="color: black;">
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($data['status'] ?? '') == $value ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cabang" class="form-label">Cabang</label>
                            <select class="form-select" id="branch_id" name="branch_id" style="color: black;">
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= ($data['branch_id'] ?? '') == $branch['id'] ? 'selected' : '' ?>><?= $branch['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="Deskripsi" class="form-label">Deskripsi</label>
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