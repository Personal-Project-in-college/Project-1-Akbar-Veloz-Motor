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
$usernameError = '';
$passwordError = '';
$oldInput = ['name' => '', 'brand_id' => ''];

$roles = $koneksi->prepare("SELECT id, name FROM roles WHERE deleted_at IS NULL AND name != 'Owner' ORDER BY name ASC");
$roles->execute();
$roles = $roles->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = generateSlug($name);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $username = trim($_POST['username']);
    $passwordInput = trim($_POST['password']);
    $role_id = trim($_POST['role_id']);


    // Validasi password
    if (strlen($passwordInput) < 8) {
        $passwordError = "Password minimal 8 karakter.";
    } else {
        $password = hashPassword($passwordInput);
    }

    // Cek data unik
    $checkStmt = $koneksi->prepare("SELECT * FROM users WHERE name = ? OR phone = ? OR username = ?");
    $checkStmt->execute([$name, $phone, $username]);
    $exist = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($exist) {
        if ($exist['name'] === $name)   $nameError = "Nama <strong>$name</strong> sudah digunakan.";
        if ($exist['phone'] === $phone) $phoneError = "No Telepon <strong>$phone</strong> sudah digunakan.";
        if ($exist['username'] === $username) $usernameError = "Username <strong>$username</strong> sudah digunakan.";
    }

    // Hanya lanjut jika tidak ada error
    if (!$nameError && !$phoneError && !$usernameError && !$passwordError) {
        try {
            $createUser = $koneksi->prepare("INSERT INTO users (name, slug, phone, address, username, password, role_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $createUser->execute([$name, $slug, $phone, $address, $username, $password, $role_id]);
            $_SESSION['success_message'] = "User <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
            header("Location: users.php");
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
            <h3 class="mb-4">Tambah Karyawan</h3>
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
                            <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                class="form-control <?= $phoneError ? 'is-invalid' : '' ?>" required>
                            <?php if ($phoneError): ?><p class="text-danger mt-1"><?= $phoneError ?></p><?php endif; ?>
                        </div>
                        <!-- Alamat Saat Ini -->
                        <div class="mb-3">
                            <label class="form-label">Alamat Saat Ini</label>
                            <textarea name="address" class="form-control" rows="8" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                class="form-control <?= $usernameError ? 'is-invalid' : '' ?>" required>
                            <?php if ($usernameError): ?><p class="text-danger mt-1"><?= $usernameError ?></p><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>"
                                class="form-control <?= $passwordError ? 'is-invalid' : '' ?>" required>
                            <?php if ($passwordError): ?><p class="text-danger mt-1"><?= $passwordError ?></p><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Jabatan</label>
                            <select name="role_id" id="role_id" class="form-control" required style="color: black;" required>
                                <option value="">Pilih Jabatan</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Error umum -->
                        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                        <!-- Tombol -->
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="users.php" class="btn btn-secondary text-white">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if (!hasAnyRole(['Owner'])): ?>
    <div class="main-panel">
        <div class="content-wrapper d-flex justify-content-center align-items-center">
            <h2 class="text-danger">Hak Akses Khusus Owner!</h2>
            <?php include '../layout/footer.php'; ?>
        </div>
    </div>
<?php endif; ?>