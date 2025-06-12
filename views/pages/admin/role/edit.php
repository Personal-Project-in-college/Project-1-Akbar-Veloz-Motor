<?php
session_start();

include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

$id = $_GET['id'] ?? null;
if (!$id) die("Error: id jabatan tidak ditemukan di URL.");

$query = $koneksi->prepare("SELECT * FROM roles WHERE id = ? AND deleted_at IS NULL");
$query->execute([$id]);
$role = $query->fetch(PDO::FETCH_ASSOC);

if (!$role) die("Error: Data jabatan tidak ditemukan atau sudah dihapus.");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    // Validasi: Cek apakah nama atau slug sudah dipakai oleh ID lain
    $checkQuery = $koneksi->prepare("SELECT COUNT(*) FROM roles WHERE (name = ?) AND id != ?");
    $checkQuery->execute([$name, $role['id']]);
    $count = $checkQuery->fetchColumn();

    if ($count > 0) {
        $error = "Nama jabatan <strong>" . htmlspecialchars($name) . "</strong> sudah digunakan oleh jabatan lain.";
    } else {
        $updateQuery = $koneksi->prepare("UPDATE roles SET name = ?, updated_at = NOW() WHERE id = ?");
        $updateQuery->execute([$name, $role['id']]);

        $_SESSION['success_message'] = "User <strong>" . htmlspecialchars($name) . "</strong> berhasil diupdate.";
        header("Location: role.php");
        exit;
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Jabatan</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control <?= $error ? 'is-invalid' : '' ?>" id="name" name="name" placeholder="Masukan Nama" value="<?= htmlspecialchars($_POST['name'] ?? $role['name']) ?>" required>
                        <?php if ($error): ?>
                            <p class="text-danger mt-1"><?= $error ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="role.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>