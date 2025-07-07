<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionResizeImage.php';
include '../../../../helpers/functionResizeImageKtp.php';

checkLogin();

$slug = $_GET['slug'] ?? null;

if (!$slug) {
    $_SESSION['error'] = "Slug pelanggan tidak valid.";
    header("Location: customer.php");
    exit;
}

// Ambil data lama
$getCustomerQuery = $koneksi->prepare("SELECT * FROM customers WHERE slug = ?");
$getCustomerQuery->execute([$slug]);
$customer = $getCustomerQuery->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data pelanggan tidak ditemukan.";
    header("Location: customers.php");
    exit;
}

$phone = $customer['phone'] ?: '-';
$waNumber = '62' . ltrim($phone, '0');
$waLink = "https://wa.me/{$waNumber}";

$isLoggedIn = $customer['is_logged_in'] == '1'
    ? '<span class="badge bg-success">Online</span>'
    : '<span class="badge bg-secondary">Offline</span>';

$isBanned = $customer['is_banned'] == '1'
    ? '<span class="badge bg-danger">Diblokir</span>'
    : '<span class="badge bg-primary">Aktif</span>';

// HTML FORM
include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Pelanggan</h3>

        <!-- Desktop View -->
        <div class="card d-none d-sm-block">
            <div class="card-body">
                <h5 class="mb-4">Informasi Pelanggan</h5>
                <table class="table">
                    <tr>
                        <th class="w-25">Nama</th>
                        <td><?= htmlspecialchars($customer['name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Status</th>
                        <td><?= $isLoggedIn ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Akses</th>
                        <td><?= $isBanned ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">No Telepon</th>
                        <td>
                            <?php if ($customer['phone']): ?>
                                <a href="<?= $waLink ?>" target="_blank" class="text-success">
                                    <?= htmlspecialchars($phone) ?> <i class="mdi mdi-whatsapp"></i>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Email</th>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Metode Daftar</th>
                        <td class="text-capitalize"><?= htmlspecialchars($customer['registration_method']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Alamat</th>
                        <td class="text-wrap"><?= htmlspecialchars($customer['address']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="card mb-4 d-block d-sm-none">
            <div class="card-body">
                <h5 class="mb-4 text-center">Informasi Pelanggan</h5>

                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Nama</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($customer['name']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Status</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= $isLoggedIn ?></div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Akses</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= $isBanned ?></div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>No Telepon</strong></div>
                    <div class="col-12 col-md-9 text-small">
                        <?php if ($customer['phone']): ?>
                            <a href="<?= $waLink ?>" target="_blank" class="text-success">
                                <?= htmlspecialchars($phone) ?> <i class="mdi mdi-whatsapp"></i>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Metode Daftar</strong></div>
                    <div class="col-12 col-md-9 text-small text-capitalize"><?= htmlspecialchars($customer['registration_method']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-12 col-md-3 mb-3"><strong>Alamat</strong></div>
                    <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($customer['address']) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
