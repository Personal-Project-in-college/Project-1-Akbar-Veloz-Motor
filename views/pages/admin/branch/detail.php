<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionGenerateSlug.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    $_SESSION['danger_message'] = "<strong>Error: </strong>Slug cabang tidak ditemukan di URL.";
    header("Location: branch.php");
    exit;
}

$getBranchQuery = $koneksi->prepare("SELECT * FROM branches WHERE slug = ? ");
$getBranchQuery->execute([$slug]);
$branch = $getBranchQuery->fetch(PDO::FETCH_ASSOC);
if (!$branch) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data cabang tidak ditemukan atau sudah dihapus.";
    header("Location: branch.php");
    exit;
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Cabang</h3>

        <!-- Desktop View -->
        <div class="card mb-4 d-none d-sm-block">
            <div class="card-body">
                <h5 class="mb-4">Informasi Cabang</h5>
                <table class="table">
                    <tr>
                        <th class="w-25">Nama</th>
                        <td><?= htmlspecialchars($branch['name']) ?></td>
                    </tr>
                    <tr>
                        <th class="w-25">Alamat</th>
                        <td class="text-wrap"><?= htmlspecialchars($branch['address']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="card mb-4 d-block d-sm-none">
            <div class="card-body">
                <h5 class="mb-4 text-center">Informasi Cabang</h5>

                <div class="row mb-3">
                    <div class="col-12 col-md-3 mb-3"><strong>Nama</strong></div>
                    <div class="col-12 col-md-9 text-small"><?= htmlspecialchars($branch['name']) ?></div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-12 col-md-3 mb-3"><strong>Alamat</strong></div>
                    <div class="col-12 col-md-9 text-small text-wrap"><?= htmlspecialchars($branch['address']) ?></div>
                </div>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>