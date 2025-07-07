<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionResizeImage.php';
include '../../../../helpers/functionResizeImageKTP.php';

$error = '';
$nameError = '';
$nikError = '';
$phoneError = '';
$emailError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = generateSlug($name);
    $nik = trim($_POST['nik']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address_ktp = trim($_POST['address_ktp']);
    $address_domicile = trim($_POST['address_domicile']);

    // Cek data unik
    $checkPartnerQuery = $koneksi->prepare("SELECT * FROM partners WHERE name = ? OR nik = ? OR phone = ? OR email = ?");
    $checkPartnerQuery->execute([$name, $nik, $phone, $email]);
    $exists = $checkPartnerQuery->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
        if ($exists['name'] === $name)   $nameError = "Nama <strong>$name</strong> sudah digunakan.";
        if ($exists['nik'] === $nik)     $nikError = "NIK <strong>$nik</strong> sudah digunakan.";
        if ($exists['phone'] === $phone) $phoneError = "No Telepon <strong>$phone</strong> sudah digunakan.";
        if ($exists['email'] === $email) $emailError = "Email <strong>$email</strong> sudah digunakan.";
    } else {
        try {
            $basePath = '../../../../storage/partners/partners_' . $slug;

            // Folder untuk KTP & foto
            $ktpFolder = $basePath . '/ktp';
            $photoFolder = $basePath . '/photo';

            // Pastikan folder ada
            if (!is_dir($ktpFolder)) mkdir($ktpFolder, 0777, true);
            if (!is_dir($photoFolder)) mkdir($photoFolder, 0777, true);

            // Fungsi upload file
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


            // Upload file
            $ktp_scan = uploadDocument('ktp_scan', $ktpFolder, $slug, 'resizeImageKTP');
            $photo    = uploadDocument('photo', $photoFolder, $slug, 'resizeImage');

            // Simpan ke DB
            $insertPartnerQuery = $koneksi->prepare("INSERT INTO partners (name, slug, nik, phone, email, ktp_scan, photo, address_ktp, address_domicile, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insertPartnerQuery->execute([$name, $slug, $nik, $phone, $email, $ktp_scan, $photo, $address_ktp, $address_domicile]);

            $_SESSION['success_message'] = "Partner <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
            header("Location: partner.php");
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
            <h3 class="mb-4">Tambah Partner</h3>
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
                        <!-- NIK -->
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" value="<?= htmlspecialchars($_POST['nik'] ?? '') ?>"
                                class="form-control <?= $nikError ? 'is-invalid' : '' ?>" required>
                            <?php if ($nikError): ?><p class="text-danger mt-1"><?= $nikError ?></p><?php endif; ?>
                        </div>
                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label">No Telepon</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
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
                        <!-- Scan KTP -->
                        <div class="mb-3">
                            <label class="form-label">Foto Scan KTP</label>
                            <input type="file" name="ktp_scan" accept="image/*" class="form-control" required>
                        </div>
                        <!-- Photo -->
                        <div class="mb-3">
                            <label class="form-label">Foto Partner</label>
                            <input type="file" name="photo" accept="image/*" class="form-control" required>
                        </div>
                        <!-- Alamat KTP -->
                        <div class="mb-3">
                            <label class="form-label">Alamat KTP</label>
                            <textarea name="address_ktp" class="form-control" rows="8" required><?= htmlspecialchars($_POST['address_ktp'] ?? '') ?></textarea>
                        </div>
                        <!-- Alamat Saat Ini -->
                        <div class="mb-3">
                            <label class="form-label">Alamat Saat Ini</label>
                            <textarea name="address_domicile" class="form-control" rows="8" required><?= htmlspecialchars($_POST['address_domicile'] ?? '') ?></textarea>
                        </div>
                        <!-- Error umum -->
                        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                        <!-- Tombol -->
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="partner.php" class="btn btn-secondary text-white">Kembali</a>
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