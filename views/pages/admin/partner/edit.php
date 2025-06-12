<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionResizeImage.php';
include '../../../../helpers/functionResizeImageKtp.php';

checkLogin();
include '../../../../helpers/functionCheckRole.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID partner tidak valid.";
    header("Location: partner.php");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM partners WHERE id = ?");
$stmt->execute([$id]);
$partner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$partner) {
    $_SESSION['error'] = "Data partner tidak ditemukan.";
    header("Location: partner.php");
    exit;
}

$error = '';
$nameError = '';
$nikError = '';
$phoneError = '';
$emailError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = generateSlug($name);
    $oldSlug = $partner['slug'];
    $oldBasePath = '../../../../storage/partners/partners_' . $oldSlug;
    $newBasePath = '../../../../storage/partners/partners_' . $slug;
    $nik = $_POST['nik'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address_ktp = $_POST['address_ktp'];
    $address_domicile = $_POST['address_domicile'];

    // Cek data unik
    $checkStmt = $koneksi->prepare("SELECT * FROM partners WHERE (name = ? OR nik = ? OR phone = ? OR email = ?) AND id <> ?");
    $checkStmt->execute([$name, $nik, $phone, $email, $id]);
    $exist = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($exist) {
        if ($exist['name'] === $name)   $nameError = "Nama <strong>$name</strong> sudah digunakan.";
        if ($exist['nik'] === $nik)     $nikError = "NIK <strong>$nik</strong> sudah digunakan.";
        if ($exist['phone'] === $phone) $phoneError = "No Telepon <strong>$phone</strong> sudah digunakan.";
        if ($exist['email'] === $email) $emailError = "Email <strong>$email</strong> sudah digunakan.";
    } else {
        try {
            // Rename folder jika slug berubah
            if ($slug !== $oldSlug && is_dir($oldBasePath)) {
                rename($oldBasePath, $newBasePath);
            }

            $basePath = '../../../../storage/partners/partners_' . $slug;
            $ktpFolder = $basePath . '/ktp';
            $photoFolder = $basePath . '/photo';

            if (!is_dir($ktpFolder)) mkdir($ktpFolder, 0777, true);
            if (!is_dir($photoFolder)) mkdir($photoFolder, 0777, true);

            function deleteDirectory($dir)
            {
                if (!file_exists($dir)) return;
                $items = scandir($dir);
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') continue;
                    $path = $dir . DIRECTORY_SEPARATOR . $item;
                    is_dir($path) ? deleteDirectory($path) : unlink($path);
                }
                rmdir($dir);
            }

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

                return 'partners/partners_' . $slug . '/' . basename($targetFolder) . '/' . $uniqueName;
            }

            $ktp_scan = $partner['ktp_scan'];
            $photo = $partner['photo'];

            // Ubah path jika slug berubah
            if ($slug !== $oldSlug) {
                if (empty($_FILES['ktp_scan']['name']) && !empty($ktp_scan)) {
                    $ktp_scan = str_replace("partners/partners_$oldSlug/", "partners/partners_$slug/", $ktp_scan);
                }
                if (empty($_FILES['photo']['name']) && !empty($photo)) {
                    $photo = str_replace("partners/partners_$oldSlug/", "partners/partners_$slug/", $photo);
                }
            }



            if (!empty($_FILES['ktp_scan']['name'])) {
                if ($ktp_scan && file_exists('../../../../storage/' . $ktp_scan)) {
                    unlink('../../../../storage/' . $ktp_scan);
                }
                $ktp_scan = uploadDocument('ktp_scan', $ktpFolder, $slug, 'resizeImageKTP');
            }


            if (!empty($_FILES['photo']['name'])) {
                if ($photo && file_exists('../../../../storage/' . $photo)) {
                    unlink('../../../../storage/' . $photo);
                }
                $photo = uploadDocument('photo', $photoFolder, $slug, 'resizeImage');
            }

            $stmt = $koneksi->prepare("UPDATE partners SET name = ?, slug = ?, nik = ?, phone = ?, email = ?, ktp_scan = ?, photo = ?, address_ktp = ?, address_domicile = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $slug, $nik, $phone, $email, $ktp_scan, $photo, $address_ktp, $address_domicile, $id]);

            $_SESSION['success'] = "Partner <strong>" . htmlspecialchars($name) . "</strong> berhasil diperbarui.";
            header("Location: partner.php");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan saat menyimpan data: " . $e->getMessage();
        }
    }
}

// HTML FORM
include '../layout/header.php';
include '../layout/sidebar.php';
?>


<?php if (hasAnyRole(['Owner'])) : ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <h3 class="mb-4">Edit Partner</h3>
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control <?= $nameError ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($partner['name']) ?>" required>
                            <?php if ($nameError): ?><p class="text-danger mt-1"><?= $nameError ?></p><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control <?= $nikError ? 'is-invalid' : '' ?>" id="nik" name="nik" value="<?= htmlspecialchars($partner['nik']) ?>" required>
                            <?php if ($nikError): ?><p class="text-danger mt-1"><?= $nikError ?></p><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">No Telepon</label>
                            <input type="text" class="form-control <?= $phoneError ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= htmlspecialchars($partner['phone']) ?>" required>
                            <?php if ($phoneError): ?><p class="text-danger mt-1"><?= $phoneError ?></p><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Aktif</label>
                            <input type="email" class="form-control <?= $emailError ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($partner['email']) ?>" required>
                            <?php if ($emailError): ?><p class="text-danger mt-1"><?= $emailError ?></p><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="ktp_scan" class="form-label">Foto Scan KTP (Kosongkan jika tidak ingin diubah)</label>
                            <input type="file" name="ktp_scan" accept="image/*" class="form-control">
                            <?php if ($partner['ktp_scan']) : ?>
                                <img src="../../../../storage/<?= $partner['ktp_scan'] ?>" width="150" class="mt-2">
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Foto Partner (Kosongkan jika tidak ingin diubah)</label>
                            <input type="file" name="photo" accept="image/*" class="form-control">
                            <?php if ($partner['photo']) : ?>
                                <img src="../../../../storage/<?= $partner['photo'] ?>" width="150" class="mt-2">
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="address_ktp" class="form-label">Alamat KTP</label>
                            <textarea class="form-control" name="address_ktp" rows="5"><?= htmlspecialchars($partner['address_ktp']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="address_domicile" class="form-label">Alamat Saat Ini</label>
                            <textarea class="form-control" name="address_domicile" rows="5"><?= htmlspecialchars($partner['address_domicile']) ?></textarea>
                        </div>

                        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="partner.php" class="btn btn-secondary mx-2 text-white">Kembali</a>
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

            <?php
            // Mengimpor bagian layout footer (termasuk tag penutup, script JS, dll).
            include '../layout/footer.php';
            ?>
        </div>
    </div>
<?php endif ?>