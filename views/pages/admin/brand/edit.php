<?php
session_start();

include '../../../../config/koneksi.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) die("Error: Slug merek tidak ditemukan di URL.");

$query = $koneksi->prepare("SELECT * FROM brands WHERE slug = ? AND deleted_at IS NULL");
$query->execute([$slug]);
$brand = $query->fetch(PDO::FETCH_ASSOC);

if (!$brand) die("Error: Data merek tidak ditemukan atau sudah dihapus.");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $newSlug = generateSlug($name);

    // Validasi: Cek apakah nama atau slug sudah dipakai oleh ID lain
    $checkQuery = $koneksi->prepare("SELECT COUNT(*) FROM brands WHERE (name = ? OR slug = ?) AND id != ?");
    $checkQuery->execute([$name, $newSlug, $brand['id']]);
    $count = $checkQuery->fetchColumn();

    if ($count > 0) {
        $error = "Nama merek <strong>" . htmlspecialchars($name) . "</strong> sudah digunakan oleh merek lain.";
    } else {
        $updateQuery = $koneksi->prepare("UPDATE brands SET name = ?, slug = ?, updated_at = NOW() WHERE id = ?");
        $updateQuery->execute([$name, $newSlug, $brand['id']]);

        $_SESSION['success'] = "Merek <strong>" . htmlspecialchars($name) . "</strong> berhasil diupdate.";
        header("Location: brand.php");
        exit;
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Merek</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control <?= $error ? 'is-invalid' : '' ?>" id="name" name="name" placeholder="Masukan Nama" value="<?= htmlspecialchars($_POST['name'] ?? $brand['name']) ?>" required>
                        <?php if ($error): ?>
                            <p class="text-danger mt-1"><?= $error ?></p>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="slug-display" value="<?= htmlspecialchars($brand['slug']) ?>" disabled>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="brand.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>