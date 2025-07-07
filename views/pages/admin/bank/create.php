<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';
include '../../../../helpers/functionShowAlert.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_name = trim($_POST['bank_name']);
    $account_number = trim($_POST['account_number']);
    $account_name = trim($_POST['account_name']);

    // Cek duplikat
    $check = $koneksi->prepare("SELECT COUNT(*) FROM banks WHERE bank_name = ?");
    $check->execute([$bank_name]);
    if ($check->fetchColumn() > 0) {
        $error = "Nama bank <strong>" . htmlspecialchars($bank_name) . "</strong> sudah ada.";
    } else {
        $insert = $koneksi->prepare("INSERT INTO banks (bank_name, account_number, account_name, created_at) VALUES (?, ?, ?, NOW())");
        $insert->execute([$bank_name, $account_number, $account_name]);

        $_SESSION['success_message'] = "Bank <strong>" . htmlspecialchars($bank_name) . "</strong> berhasil ditambahkan.";
        header("Location: bank.php");
        exit;
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Bank</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="create.php">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" name="bank_name" class="form-control <?= $error ? 'is-invalid' : '' ?>" required value="<?= htmlspecialchars($_POST['bank_name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Rekening</label>
                        <input type="text" name="account_number" class="form-control" required value="<?= htmlspecialchars($_POST['account_number'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atas Nama</label>
                        <input type="text" name="account_name" class="form-control" required value="<?= htmlspecialchars($_POST['account_name'] ?? '') ?>">
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="bank.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>