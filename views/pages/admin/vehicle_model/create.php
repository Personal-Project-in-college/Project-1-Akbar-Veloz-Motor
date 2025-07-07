<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionGenerateSlug.php';

checkLogin();

$error = '';
$nameError = '';
$oldInput = ['name' => '', 'brand_id' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $brand_id = $_POST['brand_id'];
    $slug = generateSlug($name);

    // Simpan input lama
    $oldInput['name'] = $name;
    $oldInput['brand_id'] = $brand_id;

    // Cek duplikasi berdasarkan brand_id + name
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_models WHERE brand_id = ? AND name = ?");
    $cek->execute([$brand_id, $name]);
    $exists = $cek->fetchColumn();

    if ($exists > 0) {
        $nameError = "Model <strong>" . htmlspecialchars($name) . "</strong> untuk brand ini sudah ada.";
    } else {
        try {
            $query = $koneksi->prepare("INSERT INTO vehicle_models (brand_id, name, slug, created_at) VALUES (?, ?, ?, NOW())");
            $query->execute([$brand_id, $name, $slug]);

            $_SESSION['success_message'] = "Model kendaraan <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
            header("Location: vehicle_model.php");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Ambil semua brand aktif
$brands = $koneksi->query("SELECT id, name FROM brands WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

include '../layout/header.php';
include '../layout/sidebar.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Model Kendaraan</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="create.php">
                    <!-- SELECT Brand -->
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Merek</label>
                        <select name="brand_id" id="brand_id" class="form-control" required style="color: black;">
                            <option value="" disabled <?= !$oldInput['brand_id'] ? 'selected' : '' ?>>Pilih Brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>" <?= $oldInput['brand_id'] == $brand['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Input Nama Model -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Model</label>
                        <input type="text" class="form-control <?= $nameError ? 'is-invalid' : '' ?>" id="name" name="name" required placeholder="Contoh: Avanza, Fortuner, dll" value="<?= htmlspecialchars($oldInput['name']) ?>">
                        <?php if ($nameError): ?>
                            <p class="text-danger mt-1"><?= $nameError ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="vehicle_model.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>
        <?php include '../layout/footer.php'; ?>
    </div>
</div>