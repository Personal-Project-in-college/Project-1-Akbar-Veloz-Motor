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

$phone = $partner['phone'];
$waNumber = '62' . ltrim($phone, '0');
$waLink = "https://wa.me/{$waNumber}";

// HTML FORM
include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Partner</h3>

        <!-- Desktop View -->
        <div class="card d-none d-sm-block">
            <div class="card-body">
                <h5 class="mb-4">Informasi Partner</h5>
                <table class="table">
                    <tr>
                        <th class="w-25">Nama</th>
                        <td><?= htmlspecialchars($partner['name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">NIK</th>
                        <td><?= htmlspecialchars($partner['nik']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">No Telepon</th>
                        <td><a href="<?= $waLink ?>" target="_blank" class="text-success">
                                <?= htmlspecialchars($phone) ?> <i class="mdi mdi-whatsapp"></i>
                            </a></td>
                    </tr>
                    <tr>
                        <th class="w-25">Email</th>
                        <td>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= htmlspecialchars($partner['email']) ?>"
                                target="_blank"
                                class="text-primary text-decoration-none">
                                <?= htmlspecialchars($partner['email']) ?> <i class="mdi mdi-email-outline"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Photo KTP</th>
                        <td>
                            <a href="../../../../storage/<?= $partner['ktp_scan'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                Lihat Foto KTP <i class="mdi mdi-open-in-new"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Photo</th>
                        <td>
                            <a href="../../../../storage/<?= $partner['photo'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                Lihat Foto Partner <i class="mdi mdi-open-in-new"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Alamat KTP</th>
                        <td class="text-wrap"><?= htmlspecialchars($partner['address_ktp']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Alamat Saat Ini</th>
                        <td class="text-wrap"><?= htmlspecialchars($partner['address_domicile']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="card mb-4 d-block d-sm-none">
            <div class="card-body">
                <h5 class="mb-4 text-center">Informasi Partner</h5>

                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Nama</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($partner['name']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>NIK</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($partner['nik']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>No Telepon</strong></div>
                    <a href="<?= $waLink ?>" target="_blank" class="text-success text-small">
                        <?= htmlspecialchars($phone) ?> <i class="mdi mdi-whatsapp"></i>
                    </a>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Email</strong></div>
                    <div class="col-12 col-md-9 text-small">
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= htmlspecialchars($partner['email']) ?>"
                            target="_blank"
                            class="text-primary text-decoration-none">
                            <?= htmlspecialchars($partner['email']) ?> <i class="mdi mdi-email-outline"></i>
                        </a>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Photo KTP</strong></div>
                    <div class="col-12 col-md-9 text-small d-flex justify-content-center">
                        <img src="../../../../storage/<?= $partner['ktp_scan'] ?>"
                            alt="Foto KTP"
                            class="img-fluid rounded shadow-sm"
                            style="max-width: 60%; height: auto;">
                    </div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Photo</strong></div>
                    <div class="col-12 col-md-9 text-small d-flex justify-content-center">
                        <img src="../../../../storage/<?= $partner['photo'] ?>"
                            alt="Foto Partner"
                            class="img-fluid rounded shadow-sm"
                            style="max-width: 60%; height: auto;">
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-12 col-md-3 mb-3"><strong>Alamat KTP</strong></div>
                    <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($partner['address_ktp']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-12 col-md-3 mb-3"><strong>Alamat Saat Ini</strong></div>
                    <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($partner['address_domicile']) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>