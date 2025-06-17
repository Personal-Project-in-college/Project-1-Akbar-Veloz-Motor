<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionResizeImage.php'; // kalau resize dipakai

$error = '';
$nameError = '';
$phoneError = '';
$emailError = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = generateSlug($name);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);

    // Validasi password minimal 8 karakter jika diisi
    if (!empty($password) && strlen($password) < 8) {
        $passwordError = "Password minimal 8 karakter.";
    }

    // Cek data unik
    $checkCustomerQuery = $koneksi->prepare("SELECT * FROM customers WHERE name = ? OR phone = ? OR email = ?");
    $checkCustomerQuery->execute([$name, $phone, $email]);
    $exists = $checkCustomerQuery->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
        if ($exists['name'] === $name)   $nameError = "Nama <strong>$name</strong> sudah digunakan.";
        if ($exists['phone'] === $phone) $phoneError = "No Telepon <strong>$phone</strong> sudah digunakan.";
        if ($exists['email'] === $email) $emailError = "Email <strong>$email</strong> sudah digunakan.";
    }

    // Jika tidak ada error
    if (!$nameError && !$phoneError && !$emailError && !$passwordError) {
        try {
            $basePath = '../../../../storage/customers/customer_' . $slug;
            $picturePath = $basePath . '/picture';
            if (!file_exists($picturePath)) mkdir($picturePath, 0777, true);

            function uploadDocument($inputName, $targetFolder, $slug, $resizeFunctionName = 'resizeImage')
            {
                if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) return '';

                $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
                $uniqueName = $inputName . '_' . uniqid() . '.' . $ext;
                $uploadPath = $targetFolder . '/' . $uniqueName;
                $tmpPath = $_FILES[$inputName]['tmp_name'];
                $imageExts = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array(strtolower($ext), $imageExts)) {
                    if (!function_exists($resizeFunctionName) || !$resizeFunctionName($tmpPath, $uploadPath)) {
                        return '';
                    }
                } else {
                    if (!move_uploaded_file($tmpPath, $uploadPath)) return '';
                }

                return 'customer/customer_' . $slug . '/picture/' . $uniqueName;
            }

            $photo = uploadDocument('picture', $picturePath, $slug);

            $insertCustomerQuery = $koneksi->prepare("INSERT INTO customers (name, slug, phone, email, password, picture, address, registration_method, is_banned, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'manual', 0, NOW())");
            $insertCustomerQuery->execute([$name, $slug, $phone, $email, $password, $photo, $address]);

            $_SESSION['success_message'] = "Pelanggan <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
            header("Location: customer.php");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan saat menyimpan data: " . $e->getMessage();
        }
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Pelanggan</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Nama -->
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            class="form-control <?= $nameError ? 'is-invalid' : '' ?>" required>
                        <?php if ($nameError): ?><p class="text-danger mt-1"><?= $nameError ?></p><?php endif; ?>
                    </div>
                    <!-- Phone -->
                    <div class="mb-3">
                        <label class="form-label">No Telepon</label>
                        <input type="number" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                            class="form-control <?= $phoneError ? 'is-invalid' : '' ?>" required>
                        <?php if ($phoneError): ?><p class="text-danger mt-1"><?= $phoneError ?></p><?php endif; ?>
                    </div>
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email Aktif</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            class="form-control <?= $emailError ? 'is-invalid' : '' ?>" required>
                        <?php if ($emailError): ?><p class="text-danger mt-1"><?= $emailError ?></p><?php endif; ?>
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password"
                            class="form-control <?= $passwordError ? 'is-invalid' : '' ?>">
                        <?php if ($passwordError): ?><p class="text-danger mt-1"><?= $passwordError ?></p><?php endif; ?>
                    </div>
                    <!-- Profile -->
                    <div class="mb-3">
                        <label class="form-label">Profile</label>
                        <input type="file" name="picture" class="form-control" accept="image/*">
                    </div>
                    <!-- Alamat -->
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="8" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>
                    <!-- Error umum -->
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <!-- Tombol -->
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="customer.php" class="btn btn-secondary text-white">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
