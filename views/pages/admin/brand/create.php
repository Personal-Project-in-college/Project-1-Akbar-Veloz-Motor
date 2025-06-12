<?php
session_start();

include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionShowAlert.php';
include '../../../../helpers/functionGenerateSlug.php';

$error = ''; // Untuk menampung error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = generateSlug($name);

    // Validasi duplikasi
    $checkQuery = $koneksi->prepare("SELECT COUNT(*) FROM brands WHERE name = ? OR slug = ?");
    $checkQuery->execute([$name, $slug]);
    $count = $checkQuery->fetchColumn();

    if ($count > 0) {
        $error = "Nama merek <strong>" . htmlspecialchars($name) . "</strong> sudah terdaftar.";
    } else {
        $insertQuery = $koneksi->prepare("INSERT INTO brands (name, slug, created_at) VALUES (?, ?, NOW())");
        $insertQuery->execute([$name, $slug]);

        $_SESSION['success'] = "Merek <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
        header("Location: brand.php");
        exit;
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Merek</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="create.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control <?= $error ? 'is-invalid' : '' ?>" id="name" name="name" placeholder="Masukan Nama Merek" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required autofocus>
                        <?php if ($error): ?>
                            <p class="text-danger mt-1"><?= $error ?></p>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="slug-display" id="slug-display" value="<?= generateSlug($_POST['name'] ?? '') ?>" disabled>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="brand.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>
