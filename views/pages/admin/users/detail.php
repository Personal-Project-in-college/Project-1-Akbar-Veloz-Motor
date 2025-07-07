<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['danger_message'] = "<strong>Error: </strong>Id karyawan tidak ditemukan di URL.";
    header("Location: users.php");
    exit;
}

$getUserQuery = $koneksi->prepare("SELECT users.*, roles.name AS role_name FROM users LEFT JOIN roles ON users.role_id = roles.id WHERE users.id = ? ");
$getUserQuery->execute([$id]);
$user = $getUserQuery->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data karyawan tidak ditemukan atau sudah dihapus.";
    header("Location: branch.php");
    exit;
}

$phone = $user['phone'];
$waNumber = '62' . ltrim($phone, '0');
$waLink = "https://wa.me/{$waNumber}";

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Karyawan</h3>

        <!-- Desktop View -->
        <div class="card mb-4 d-none d-sm-block">
            <div class="card-body">
                <h5 class="mb-4">Informasi Karyawan</h5>
                <table class="table">
                    <tr>
                        <th class="w-25">Nama</th>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">No Telepon</th>
                        <td>
                            <div class="col-12 col-md-9">
                                <a href="<?= $waLink ?>" target="_blank" class="text-success">
                                    <?= htmlspecialchars($phone) ?> <i class="mdi mdi-whatsapp"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">Username</th>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Password</th>
                        <td>******</td>
                    </tr>
                    <tr>
                        <th class="w-25">Jabatan</th>
                        <td><?= htmlspecialchars($user['role_name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Alamat</th>
                        <td class="text-wrap"><?= htmlspecialchars($user['address']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="card mb-4 d-block d-sm-none">
            <div class="card-body">
                <h5 class="mb-4 text-center">Informasi Karyawan</h5>

                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Nama</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($user['name']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>No Telepon</strong></div>
                    <div class="col-12 col-md-9 text-small">
                        <a href="<?= $waLink ?>" target="_blank" class="text-success">
                            <?= htmlspecialchars($phone) ?> <i class="mdi mdi-whatsapp"></i>
                        </a>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Username</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($user['username']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Password</strong></div>
                    <div class="col-12 col-md-9 text-small">******</div>
                </div>
                <hr class="my-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Jabatan</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($user['role_name']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-12 col-md-3 mb-3"><strong>Alamat</strong></div>
                    <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($user['address']) ?></div>
                </div>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>