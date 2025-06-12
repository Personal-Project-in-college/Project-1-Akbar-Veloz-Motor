<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    $_SESSION['danger_message'] = "<strong>Error: </strong>Slug cabang tidak ditemukan di URL.";
    header("Location: branch.php");
    exit;
}

$getBranchQuery = $koneksi->prepare("SELECT * FROM branches WHERE slug = ? AND deleted_at IS NULL");
$getBranchQuery->execute([$slug]);
$branch = $getBranchQuery->fetch(PDO::FETCH_ASSOC);
if (!$branch) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data cabang tidak ditemukan atau sudah dihapus.";
    header("Location: branch.php");
    exit;
}

$error = '';
$nameError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $newSlug = generateSlug($name);

    // Validasi: cek nama/slug sudah digunakan cabang lain
    $checkQuery = $koneksi->prepare("SELECT COUNT(*) FROM branches WHERE (name = ? OR slug = ?) AND id != ?");
    $checkQuery->execute([$name, $newSlug, $branch['id']]);
    $exists = $checkQuery->fetchColumn();

    if ($exists > 0) {
        $nameError = "Nama cabang <strong>" . htmlspecialchars($name) . "</strong> sudah digunakan.";
    } else {
        try {
            $updateQuery = $koneksi->prepare("UPDATE branches SET name = ?, slug = ?, address = ?, updated_at = NOW() WHERE id = ?");
            $updateQuery->execute([$name, $newSlug, $address, $branch['id']]);

            $_SESSION['success'] = "Cabang <strong>" . htmlspecialchars($name) . "</strong> berhasil diupdate.";
            header("Location: branch.php");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan saat update data: " . $e->getMessage();
        }
    }

    // Update data input di form saat error
    $branch['name'] = $name;
    $branch['address'] = $address;
    $branch['slug'] = $newSlug;
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<?php if (hasAnyRole(['Owner'])) : ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <h3 class="mb-4">Edit Cabang</h3>

            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control <?= $nameError ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($branch['name']) ?>" required>
                            <?php if ($nameError): ?>
                                <p class="text-danger mt-1"><?= $nameError ?></p>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="slug-display" value="<?= htmlspecialchars($branch['slug']) ?>" disabled>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="5" required><?= htmlspecialchars($branch['address']) ?></textarea>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="branch.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                    </form>
                </div>
            </div>

            <?php include '../layout/footer.php'; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!hasAnyRole(['Owner'])) : ?>
    <div class="main-panel">
        <div class="content-wrapper d-flex justify-content-center align-items-center">
            <h2 class="mb-4 text-danger"><u><strong>Hak Akses Khusus Owner !</strong></u></h2>
            <?php include '../layout/footer.php'; ?>
        </div>
    </div>
<?php endif; ?>