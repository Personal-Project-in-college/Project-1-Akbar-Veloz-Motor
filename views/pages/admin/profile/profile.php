<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionResizeImage.php';

checkLogin();

$userId = $_SESSION['user_id'];

// Ambil data user
$getUserQuery = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$getUserQuery->execute([$userId]);
$user = $getUserQuery->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> User tidak ditemukan.";
    header("Location: ../dashboard/index.php");
    exit;
}

$photo = $user['photo'];
$errors = [];
$formOld = [];

function uploadProfilePhoto($inputName, $targetFolder, $slug)
{
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) return '';

    $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
    $uniqueName = 'photo_' . uniqid() . '.' . $ext;
    $uploadPath = $targetFolder . '/' . $uniqueName;
    $tmpPath = $_FILES[$inputName]['tmp_name'];
    $imageExts = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array(strtolower($ext), $imageExts)) {
        if (!resizeImage($tmpPath, $uploadPath)) return '';
    } else {
        if (!move_uploaded_file($tmpPath, $uploadPath)) return '';
    }

    return 'users/user_' . $slug . '/' . $uniqueName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];

    $formOld = $_POST;

    $checkUserNameQuery = $koneksi->prepare("SELECT COUNT(*) FROM users WHERE name = ? AND id != ?");
    $checkUserNameQuery->execute([$name, $userId]);
    if ($checkUserNameQuery->fetchColumn() > 0) {
        $errors['name'] = "Nama sudah digunakan.";
    }

    // Validasi unik username (kecuali dirinya sendiri)
    $checkUserUsernameQuery = $koneksi->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
    $checkUserUsernameQuery->execute([$username, $userId]);
    if ($checkUserUsernameQuery->fetchColumn() > 0) {
        $errors['username'] = "Username sudah digunakan.";
    }

    // Validasi password jika diisi
    if (!empty($password) && strlen($password) < 8) {
        $errors['password'] = "Password minimal 8 karakter.";
    }

    // Kalau ada error, kembalikan ke form
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_old'] = $formOld;
        header("Location: profile.php");
        exit;
    }

    // Proses update
    $oldSlug = $user['slug'];
    $newSlug = generateSlug($name);

    $oldFolder = "../../../../storage/users/user_" . $oldSlug;
    $newFolder = "../../../../storage/users/user_" . $newSlug;

    // Rename folder jika slug berubah
    if ($oldSlug !== $newSlug && is_dir($oldFolder)) {
        rename($oldFolder, $newFolder);
    }

    if (!is_dir($newFolder)) {
        mkdir($newFolder, 0777, true);
    }

    // Update path photo jika slug berubah
    if (!empty($photo) && $oldSlug !== $newSlug) {
        $fileName = basename($photo);
        $photo = "users/user_" . $newSlug . "/" . $fileName;
    }

    // Jika upload foto baru
    if (!empty($_FILES['photo']['name'])) {
        if ($photo && file_exists("../../../../storage/" . $photo)) {
            unlink("../../../../storage/" . $photo);
        }
        $photo = uploadProfilePhoto('photo', $newFolder, $newSlug);
    }

    $updateFields = "name = ?, slug = ?, username = ?, phone = ?, address = ?, photo = ?";
    $params = [$name, $newSlug, $username, $phone, $address, $photo];

    if (!empty($password)) {
        $updateFields .= ", password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    $params[] = $userId;

    $updateQuery = $koneksi->prepare("UPDATE users SET $updateFields, updated_at = NOW() WHERE id = ?");
    $updateQuery->execute($params);

    $_SESSION['success_message'] = "Profile <strong>" . htmlspecialchars($name) . "</strong> berhasil diupdate.";
    header("Location: ../dashboard/index.php");
    exit;
}

// Ambil pesan error & input lama
$formErrors = $_SESSION['form_errors'] ?? [];
$formOld = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);
?>

<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Profil</h3>
        <div class="card">
            <div class="card-body">
                <?php if (!empty($photo)) : ?>
                    <div class="text-center mb-4">
                        <img src="../../../../storage/<?= $photo ?>" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                    </div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($formOld['name'] ?? $user['name']) ?>" required>
                        <?php if (!empty($formErrors['name'])) : ?>
                            <small class="text-danger"><?= $formErrors['name'] ?></small>
                        <?php endif; ?>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($formOld['username'] ?? $user['username']) ?>" required>
                        <?php if (!empty($formErrors['username'])) : ?>
                            <small class="text-danger"><?= $formErrors['username'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($formOld['phone'] ?? $user['phone']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control"><?= htmlspecialchars($formOld['address'] ?? $user['address']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Profil (opsional)</label>
                        <input type="file" name="photo" accept="image/*" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                        <?php if (!empty($formErrors['password'])) : ?>
                            <small class="text-danger"><?= $formErrors['password'] ?></small>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="../dashboard/index.php" class="btn btn-secondary mx-2">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>