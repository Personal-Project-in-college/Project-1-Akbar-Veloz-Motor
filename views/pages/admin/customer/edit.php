<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionHashPassword.php';

$error = '';
$nameError = '';
$phoneError = '';
$emailError = '';
$passwordError = '';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Slug pelanggan tidak ditemukan di URL.";
    header("Location: customer.php");
    exit;
};

$getQuery = $koneksi->prepare("SELECT * FROM customers WHERE slug = ?");
$getQuery->execute([$slug]);
$customer = $getQuery->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data pelanggan tidak ditemukan atau sudah dihapus.";
    header("Location: customer.php");
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $newSlug = generateSlug($name);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $is_banned = isset($_POST['is_banned']) ? 1 : 0;

    $checkQuery = $koneksi->prepare("SELECT * FROM customers WHERE (name = ? OR phone = ? OR email = ?) AND id != ?");
    $checkQuery->execute([$name, $phone, $email, $customer['id']]);
    $exists = $checkQuery->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
        if ($exists['name'] === $name)   $nameError = "Nama <strong>$name</strong> sudah digunakan.";
        if ($exists['phone'] === $phone) $phoneError = "No Telepon <strong>$phone</strong> sudah digunakan.";
        if ($exists['email'] === $email) $emailError = "Email <strong>$email</strong> sudah digunakan.";
    } elseif (!empty($password) && strlen($password) < 8) {
        $passwordError = "Password minimal 8 karakter.";
    } else {
        try {
            $query = "UPDATE customers SET name = ?, slug = ?, phone = ?, email = ?, address = ?, is_banned = ?, updated_at = NOW()";
            $params = [$name, $newSlug, $phone, $email, $address, $is_banned];

            if (!empty($password)) {
                $query .= ", password = ?";
                $params[] = hashPassword($password);
            }

            $query .= " WHERE id = ?";
            $params[] = $customer['id'];

            $stmt = $koneksi->prepare($query);
            $stmt->execute($params);

            $_SESSION['success_message'] = "Pelanggan <strong>" . htmlspecialchars($name) . "</strong> berhasil diperbarui.";
            header("Location: customer.php");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan saat memperbarui data: " . $e->getMessage();
        }
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Pelanggan</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $customer['name']) ?>" class="form-control <?= $nameError ? 'is-invalid' : '' ?>" required>
                        <?php if ($nameError): ?><p class="text-danger mt-1"><?= $nameError ?></p><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Telepon</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? $customer['phone']) ?>" class="form-control <?= $phoneError ? 'is-invalid' : '' ?>" required>
                        <?php if ($phoneError): ?><p class="text-danger mt-1"><?= $phoneError ?></p><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Aktif</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $customer['email']) ?>" class="form-control <?= $emailError ? 'is-invalid' : '' ?>" required>
                        <?php if ($emailError): ?><p class="text-danger mt-1"><?= $emailError ?></p><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control <?= $passwordError ? 'is-invalid' : '' ?>">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        <?php if ($passwordError): ?><p class="text-danger mt-1"><?= $passwordError ?></p><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="6" required><?= htmlspecialchars($_POST['address'] ?? $customer['address']) ?></textarea>
                    </div>
                    <div class="mb-3 form-switch">
                        <input type="checkbox" class="form-check-input" id="is_banned" name="is_banned" <?= ($customer['is_banned'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label mx-2" for="is_banned">Blokir Akun</label>
                    </div>
                    <?php if ($error): ?><div class="alert alert-danger mt-2"><?= $error ?></div><?php endif; ?>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="customer.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
