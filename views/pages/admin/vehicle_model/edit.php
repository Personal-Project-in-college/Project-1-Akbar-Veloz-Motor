<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionGenerateSlug.php';

checkLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan.");
}

// Ambil data model berdasarkan slug
$stmt = $koneksi->prepare("SELECT * FROM vehicle_models WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$model = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$model) {
    die("Data model kendaraan tidak ditemukan.");
}

// Ambil semua brand aktif
$brands = $koneksi->query("SELECT id, name FROM brands WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Variabel pesan error
$nameError = '';
$error = '';
$oldInput = [
    'name' => $model['name'],
    'brand_id' => $model['brand_id'],
];

// Handle submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $brand_id = $_POST['brand_id'];
    $slug_baru = generateSlug($name);

    $oldInput['name'] = $name;
    $oldInput['brand_id'] = $brand_id;

    // Cek duplikat: nama + brand_id tapi BUKAN data ini sendiri
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_models WHERE brand_id = ? AND name = ? AND id != ?");
    $cek->execute([$brand_id, $name, $model['id']]);
    $exists = $cek->fetchColumn();

    if ($exists > 0) {
        $nameError = "Model <strong>" . htmlspecialchars($name) . "</strong> untuk brand ini sudah ada.";
    } else {
        try {
            $update = $koneksi->prepare("UPDATE vehicle_models SET brand_id = ?, name = ?, slug = ?, updated_at = NOW() WHERE id = ?");
            $update->execute([$brand_id, $name, $slug_baru, $model['id']]);

            $_SESSION['success'] = "Model kendaraan <strong>" . htmlspecialchars($name) . "</strong> berhasil diupdate.";
            header("Location: vehicle_model.php");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Model Kendaraan</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <!-- Select Brand -->
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

                    <!-- Input Nama -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Model</label>
                        <input type="text" class="form-control <?= $nameError ? 'is-invalid' : '' ?>" id="name" name="name" required value="<?= htmlspecialchars($oldInput['name']) ?>">
                        <?php if ($nameError): ?>
                            <p class="text-danger mt-1"><?= $nameError ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="vehicle_model.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>
        <?php include '../layout/footer.php'; ?>
    </div>
</div>