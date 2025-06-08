<?php

/**
 * File: edit.php
 * Deskripsi: Halaman untuk mengedit data kendaraan.
 * - Saat diakses dengan metode GET, halaman ini akan menampilkan form yang sudah terisi data lama kendaraan.
 * - Saat form di-submit dengan metode POST, halaman ini akan memproses dan meng-update data ke database.
 */

session_start(); // Memulai sesi untuk pesan notifikasi.

// 1. Koneksi dan Pengecekan Login
include '../../../../config/koneksi.php';              // Menghubungkan ke database.
include '../../../../helpers/functionCheckLogin.php'; // Memuat fungsi helper.
checkLogin();                                         // Memastikan pengguna sudah login.

// 2. Pengambilan dan Validasi ID Kendaraan
// 🪢 Ambil id kendaraan dari URL, jika tidak ada, beri nilai null.
$id = $_GET['id'] ?? null;

// Jika tidak ada ID di URL, hentikan eksekusi skrip.
if (!$id) {
    die("Akses tidak valid. ID kendaraan tidak ditemukan.");
}

// 3. Pengambilan Data Kendaraan dari Database
// 🪢 Ambil data kendaraan yang sesuai dengan ID dari URL.
$data = $koneksi->prepare("SELECT * FROM vehicles WHERE id = ?");
$data->execute([$id]);
$vehicle = $data->fetch(PDO::FETCH_ASSOC);

// Jika data dengan ID tersebut tidak ada di database, hentikan eksekusi.
if (!$vehicle) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Kendaraan dengan ID " . htmlspecialchars($id) . " tidak ditemukan.");
}

// Pengecekan Logika Bisnis: Mencegah edit jika data dinonaktifkan oleh cabang.
// Catatan: Pengecekan `!$vehicle` di sini sedikit berulang karena sudah dicek di atas, namun kondisi kedua sangat penting.
if ($vehicle['deleted_at'] !== null || $vehicle['deleted_by_branch_at'] !== null) {
    die("Data Kendaraan tidak bisa diedit karena sedang dinonaktifkan.");
}

// 4. Proses Update Data (Jika Form di-Submit)
// 🔝 Cek apakah request yang masuk adalah POST, yang artinya pengguna menekan tombol "Update".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Ambil semua data yang dikirim dari form.
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
    $user = $_SESSION['user_id']; // ID pengguna yang sedang login.
    $branch = $_POST['branch_id'];

    // ⬇️ Siapkan dan jalankan query UPDATE ke database.
    // Perhatikan: `updated_at=NOW()` otomatis mencatat waktu perubahan.
    // `deleted_by_branch_at=NULL` mengaktifkan kembali jika sebelumnya dinonaktifkan oleh cabang.
    $updateQuery = $koneksi->prepare(
        "UPDATE vehicles SET 
        brand_model=?, type_vehicle=?, color=?, production_year=?, serial_number=?, 
        stnk_deadline=?, kilometer=?, cc_engine=?, description=?, price=?, status=?, 
        user_id=?, branch_id=?, updated_at=NOW(), deleted_by_branch_at=NULL 
        WHERE id=?"
    );
    $updateQuery->execute([$brand, $type, $color, $production, $serial, $stnk, $kilometer, $cc, $desc, $price, $status, $user, $branch, $id]);

    // Siapkan pesan notifikasi sukses.
    $_SESSION['success'] = "Kendaraan <strong>" . htmlspecialchars($id) . "</strong> berhasil diupdate.";

    // 🚀 Setelah berhasil, alihkan pengguna kembali ke halaman daftar kendaraan.
    header("Location: vehicles.php");
    exit;
}

// 5. Pengambilan Data Pendukung untuk Form
// Data ini dibutuhkan untuk mengisi pilihan pada elemen <select> di form.

// 🔍 Ambil hanya data cabang yang aktif (belum di-soft delete).
$branches = $koneksi->query("SELECT * FROM branches WHERE deleted_at IS NULL")->fetchAll();

// Siapkan array untuk pilihan jenis kendaraan.
$types = [
    'motorcycle' => 'Motor',
    'car' => 'Mobil'
];

// Siapkan array untuk pilihan status kendaraan.
$statuses = [
    'available' => 'Tersedia',
    'service' => 'Service',
    'test_drive' => 'Tes Jalan',
    'sold' => 'Terjual'
];

// 6. Tampilkan Layout dan Form HTML
include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Kendaraan</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="kode_kendaraan" class="form-label">Kode Kendaraan</label>
                        <input type="text" class="form-control" id="id_show" name="id_show" value="<?= htmlspecialchars($vehicle['id']) ?>" disabled>
                        <input type="hidden" class="form-control" id="id" name="id" value="<?= htmlspecialchars($vehicle['id']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="brand" class="form-label">Brand & Model</label>
                        <input type="text" class="form-control" id="brand_model" name="brand_model" value="<?= htmlspecialchars($vehicle['brand_model']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                        <select class="form-select" id="type_vehicle" name="type_vehicle" style="color: black;">
                            <?php foreach ($types as $key => $label) : ?>
                                <option value="<?= $key ?>" <?= $vehicle['type_vehicle'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="warna" class="form-label">Warna</label>
                        <input type="text" class="form-control" id="color" name="color" value="<?= htmlspecialchars($vehicle['color']) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tahun_produksi" class="form-label">Tahun Produksi</label>
                            <input type="date" class="form-control" id="production_year" name="production_year" value="<?= htmlspecialchars($vehicle['production_year']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nomor_mesin" class="form-label">Nomor Mesin</label>
                            <input type="number" class="form-control" id="serial_number" name="serial_number" value="<?= htmlspecialchars($vehicle['serial_number']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tenggat_SNTK" class="form-label">Tenggat STNK</label>
                            <input type="date" class="form-control" id="stnk_deadline" name="stnk_deadline" value="<?= htmlspecialchars($vehicle['stnk_deadline']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kilometer" class="form-label">Kilometer</label>
                            <input type="number" class="form-control" id="kilometer" name="kilometer" value="<?= htmlspecialchars($vehicle['kilometer']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cc_mesin" class="form-label">CC Mesin</label>
                            <input type="number" class="form-control" id="cc_engine" name="cc_engine" value="<?= htmlspecialchars($vehicle['cc_engine']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($vehicle['price']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" style="color: black;">
                                <?php foreach ($statuses as $key => $label) : ?>
                                    <option value="<?= $key ?>" <?= $vehicle['status'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cabang" class="form-label">Cabang</label>
                            <select class="form-select" id="branch_id" name="branch_id" style="color: black;">
                                <?php foreach ($branches as $branch) : ?>
                                    <option value="<?= $branch['id'] ?>" <?= $vehicle['branch_id'] == $branch['id'] ? 'selected' : '' ?>><?= htmlspecialchars($branch['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="Deskripsi" class="form-label">Deskripsi</label>
                        <textarea cols="15" rows="15" class="form-control" id="description" name="description" placeholder="Masukan Deskripsi"><?= htmlspecialchars($vehicle['description']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="vehicles.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>