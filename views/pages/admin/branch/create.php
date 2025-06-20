<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionGenerateSlug.php';

$error = ''; // untuk error umum
$nameError = ''; // khusus error nama

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = generateSlug($name);
    $address = trim($_POST['address']);

    // Cek apakah nama atau slug sudah ada
    $checkBranchQuery = $koneksi->prepare("SELECT COUNT(*) FROM branches WHERE name = ? OR slug = ?");
    $checkBranchQuery->execute([$name, $slug]);
    $exists = $checkBranchQuery->fetchColumn();

    if ($exists > 0) {
        $nameError = "Nama cabang <strong>" . htmlspecialchars($name) . "</strong> sudah terdaftar.";
    } else {
        try {
            $insertBranchQuery = $koneksi->prepare("INSERT INTO branches (name, slug, address, created_at) VALUES (?, ?, ?, NOW())");
            $insertBranchQuery->execute([$name, $slug, $address]);

            $_SESSION['success_message'] = "Cabang <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
            header("Location: branch.php");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan saat menyimpan data: " . $e->getMessage();
        }
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<?php if (hasAnyRole(['Owner'])) : ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <h3 class="mb-4">Tambah Cabang</h3>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="create.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control <?= $nameError ? 'is-invalid' : '' ?>" id="name" name="name" placeholder="Masukan Nama Cabang" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" autofocus>
                            <?php if ($nameError): ?>
                                <p class="text-danger mt-1"><?= $nameError ?></p>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="slug-display" id="slug-display" value="<?= generateSlug($_POST['name'] ?? '') ?>" disabled>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="8" placeholder="Masukan Alamat Lengkap Cabang" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="branch.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                    </form>
                </div>
            </div>
            <?php include '../layout/footer.php'; ?>
        </div>
    </div>
<?php endif ?>

<?php if (!hasAnyRole(['Owner'])) : ?>
    <div class="main-panel">
        <div class="content-wrapper d-flex justify-content-center align-items-center">
            <h2 class="mb-4 text-danger"><u><strong>Hak Akses Khusus Owner !</strong></u></h2>
            <?php include '../layout/footer.php'; ?>
        </div>
    </div>
<?php endif ?>