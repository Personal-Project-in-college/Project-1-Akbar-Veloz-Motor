<?php
session_start();

include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> ID bank tidak ditemukan di URL.";
    header("Location: bank.php");
    exit;
}

$getBankQuery = $koneksi->prepare("SELECT * FROM banks WHERE id = ? AND deleted_at IS NULL");
$getBankQuery->execute([$id]);
$bank = $getBankQuery->fetch(PDO::FETCH_ASSOC);

if (!$bank) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data bank tidak ditemukan atau sudah dihapus.";
    header("Location: bank.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_name = trim($_POST['bank_name']);
    $account_number = trim($_POST['account_number']);
    $account_name = trim($_POST['account_name']);
    $is_active = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 0;


    // Validasi: Nama bank unik (selain milik ID ini)
    $checkQuery = $koneksi->prepare("SELECT COUNT(*) FROM banks WHERE bank_name = ? AND id != ?");
    $checkQuery->execute([$bank_name, $id]);
    $exist = $checkQuery->fetchColumn();

    if ($exist > 0) {
        $error = "Nama bank <strong>" . htmlspecialchars($bank_name) . "</strong> sudah digunakan oleh bank lain.";
    } else {
        $updateQuery = $koneksi->prepare("UPDATE banks SET bank_name = ?, account_number = ?, account_name = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
        $updateQuery->execute([$bank_name, $account_number, $account_name, $is_active, $id]);

        $_SESSION['success_message'] = "Bank <strong>" . htmlspecialchars($bank_name) . "</strong> berhasil diperbarui.";
        header("Location: bank.php");
        exit;
    }
}

include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Bank</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" name="bank_name" class="form-control <?= $error ? 'is-invalid' : '' ?>" required value="<?= htmlspecialchars($_POST['bank_name'] ?? $bank['bank_name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Rekening</label>
                        <input type="text" name="account_number" class="form-control" required value="<?= htmlspecialchars($_POST['account_number'] ?? $bank['account_number']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atas Nama</label>
                        <input type="text" name="account_name" class="form-control" required value="<?= htmlspecialchars($_POST['account_name'] ?? $bank['account_name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Bank</label>
                        <select name="is_active" class="form-select text-dark" required>
                            <option value="1" <?= (($_POST['is_active'] ?? $bank['is_active']) == 1) ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= (($_POST['is_active'] ?? $bank['is_active']) == 0) ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="bank.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php include '../layout/footer.php'; ?>
    </div>
</div>