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

        <!-- TABEL Cabang -->
        <div class="card mb-4">
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

        <?php include '../layout/footer.php'; ?>
    </div>
</div>