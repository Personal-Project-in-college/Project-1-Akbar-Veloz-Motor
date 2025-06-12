<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionResizeImage.php';

checkLogin();

$userId = $_SESSION['user_id'];

$stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "Data user tidak ditemukan.";
    header("Location: ../dashboard.php");
    exit;
}

$photo = $user['photo'];

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
    $oldSlug = $user['slug'];
    $oldFolder = "../../../../storage/users/user_" . $oldSlug;
    $newSlug = generateSlug($_POST['name']);
    $newFolder = "../../../../storage/users/user_" . $newSlug;

    // Jika slug berubah dan folder lama ada, rename
    if ($oldSlug !== $newSlug && is_dir($oldFolder)) {
        rename($oldFolder, $newFolder);
    }

    // Kalau folder belum ada setelah rename, baru bikin
    if (!is_dir($newFolder)) {
        mkdir($newFolder, 0777, true);
    }

    // Update path photo jika hanya slug berubah, dan photo lama masih ada
    if (!empty($user['photo']) && $oldSlug !== $newSlug) {
        $fileName = basename($user['photo']);
        $photo = "users/user_" . $newSlug . "/" . $fileName;
    }

    // Upload foto baru jika ada
    if (!empty($_FILES['photo']['name'])) {
        if ($photo && file_exists("../../../../storage/" . $photo)) {
            unlink("../../../../storage/" . $photo);
        }
        $photo = uploadProfilePhoto('photo', $newFolder, $newSlug);
    }

    $name = $_POST['name'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    $updateFields = "name = ?, slug = ?, username = ?, phone = ?, address = ?, photo = ?";
    $params = [$name, $newSlug, $username, $phone, $address, $photo];

    if (!empty($password)) {
        $updateFields .= ", password = ?";
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $params[] = $hashedPassword;
    }

    $params[] = $userId;

    $stmt = $koneksi->prepare("UPDATE users SET $updateFields, updated_at = NOW() WHERE id = ?");
    $stmt->execute($params);

    $_SESSION['success'] = "Profil berhasil diperbarui.";
    header("Location: ../dashboard/index.php");
    exit;
}

// Fungsi upload seperti sebelumnya

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
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Profil (opsional)</label>
                        <input type="file" name="photo" accept="image/*" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="../dashboard.php" class="btn btn-secondary mx-2">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>