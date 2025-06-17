<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionResizeImage.php';
include '../../../../helpers/functionResizeImageKtp.php';

checkLogin();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID partner tidak valid.";
    header("Location: partner.php");
    exit;
}

// Ambil data lama
$getPartnerQuery = $koneksi->prepare("SELECT * FROM partners WHERE id = ?");
$getPartnerQuery->execute([$id]);
$partner = $getPartnerQuery->fetch(PDO::FETCH_ASSOC);

if (!$partner) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data partner tidak ditemukan.";
    header("Location: partner.php");
    exit;
}


// HTML FORM
include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Partner</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($partner['name']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control" id="nik" name="nik" value="<?= htmlspecialchars($partner['nik']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">No Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($partner['phone']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Aktif</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($partner['email']) ?>" disabled>
                    </div>

                    <style>
                        .fullscreen-img {
                            width: 150px;
                            cursor: zoom-in;
                            transition: 0.3s;
                        }

                        .fullscreen-overlay {
                            display: none;
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100vw;
                            height: 100vh;
                            background-color: rgba(0, 0, 0, 0.9);
                            justify-content: center;
                            align-items: center;
                            z-index: 9999;
                        }

                        .fullscreen-overlay img {
                            max-width: 90%;
                            max-height: 90%;
                            object-fit: contain;
                            cursor: zoom-out;
                        }
                    </style>

                    <div class="mb-3">
                        <label class="form-label">Dokumen Gambar</label>
                        <div class="d-flex gap-4">
                            <?php if ($partner['ktp_scan']) : ?>
                                <div class="text-center">
                                    <img src="../../../../storage/<?= $partner['ktp_scan'] ?>" class="fullscreen-img" onclick="openFullscreen(this)">
                                    <p class="mt-2 mb-0">Scan KTP</p>
                                </div>
                            <?php endif; ?>

                            <?php if ($partner['photo']) : ?>
                                <div class="text-center">
                                    <img src="../../../../storage/<?= $partner['photo'] ?>" class="fullscreen-img" onclick="openFullscreen(this)">
                                    <p class="mt-2 mb-0">Foto Partner</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="fullscreenOverlay" class="fullscreen-overlay" onclick="closeFullscreen()">
                        <img id="fullscreenImage" src="" alt="Full Image">
                    </div>

                    <script>
                        function openFullscreen(imgElement) {
                            const overlay = document.getElementById('fullscreenOverlay');
                            const fullscreenImg = document.getElementById('fullscreenImage');
                            fullscreenImg.src = imgElement.src;
                            overlay.style.display = 'flex';
                        }

                        function closeFullscreen() {
                            const overlay = document.getElementById('fullscreenOverlay');
                            overlay.style.display = 'none';
                            document.getElementById('fullscreenImage').src = '';
                        }
                    </script>

                    <div class="mb-3">
                        <label for="address_ktp" class="form-label">Alamat KTP</label>
                        <textarea disabled class="form-control" name="address_ktp" rows="5"><?= htmlspecialchars($partner['address_ktp']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="address_domicile" class="form-label">Alamat Saat Ini</label>
                        <textarea disabled class="form-control" name="address_domicile" rows="5"><?= htmlspecialchars($partner['address_domicile']) ?></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>