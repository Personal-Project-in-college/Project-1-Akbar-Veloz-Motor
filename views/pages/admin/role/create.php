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

    // Validasi duplikasi
    $checkQuery = $koneksi->prepare("SELECT COUNT(*) FROM roles WHERE name = ?");
    $checkQuery->execute([$name]);
    $count = $checkQuery->fetchColumn();

    if ($count > 0) {
        $error = "Nama jabatan <strong>" . htmlspecialchars($name) . "</strong> sudah terdaftar.";
    } else {
        $insertQuery = $koneksi->prepare("INSERT INTO roles (name, created_at) VALUES (?, NOW())");
        $insertQuery->execute([$name]);

        $_SESSION['success_message'] = "User <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
        header("Location: role.php");
        exit;
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Jabatan</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="create.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control <?= $error ? 'is-invalid' : '' ?>" id="name" name="name" placeholder="Masukan Nama Jabatan" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required autofocus>
                        <?php if ($error): ?>
                            <p class="text-danger mt-1"><?= $error ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="role.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>
