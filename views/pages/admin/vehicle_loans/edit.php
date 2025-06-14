<?php

session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

if (!isset($_GET['id'])) {
    $_SESSION['danger_message'] = "<strong>Error: </strong>ID peminjaman kendaraan tidak ditemukan di URL.";
    header('Location: vehicle_loans.php');
    exit;
}

$id = $_GET['id'];

// Ambil data peminjaman berdasarkan ID
$getVehicleLoanQuery = $koneksi->prepare("SELECT vehicle_loans.*, partners.name AS partner_name FROM vehicle_loans LEFT JOIN partners ON vehicle_loans.partner_id = partners.id WHERE vehicle_loans.id = ?");
$getVehicleLoanQuery->execute([$id]);
$data = $getVehicleLoanQuery->fetch();

if (!$data) {
    $_SESSION['danger_message'] = "<strong>Error: </strong> Data peminjaman kendaraan tidak ditemukan atau sudah dihapus.";
    header('Location: vehicle_loans.php');
    exit;
}

// Ambil semua kendaraan
$vehicles = $koneksi->query("SELECT id FROM vehicles WHERE deleted_at IS NULL AND deleted_by_branch_at IS NULL");

// Ambil semua partner
$partners = $koneksi->query("SELECT * FROM partners WHERE deleted_at IS NULL");

$returnDateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id_new = $_POST['vehicle_id'];
    $partner_id = $_POST['partner_id'];
    $loan_date = $_POST['loan_date'];
    $return_date = $_POST['return_date'];
    $note = $_POST['note'];
    $status = $_POST['status'];

    $loanTimestamp = strtotime($loan_date);
    $returnTimestamp = strtotime($return_date);

    if ($returnTimestamp <= $loanTimestamp) {
        $returnDateError = "Waktu pengembalian tidak boleh sebelum waktu pinjam.";
    } elseif (($returnTimestamp - $loanTimestamp) > (7 * 24 * 60 * 60)) {
        $returnDateError = "Maksimal waktu peminjaman adalah 7 hari.";
    }

    if ($returnDateError === '') {
        // Update status kendaraan
        if ($vehicle_id_new != $data['vehicle_id']) {
            $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?")->execute([$data['vehicle_id']]);
            $koneksi->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?")->execute([$vehicle_id_new]);
        } elseif ($status == 'returned') {
            $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?")->execute([$vehicle_id_new]);
        } else {
            $koneksi->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?")->execute([$vehicle_id_new]);
        }

        $updateQuery = $koneksi->prepare("UPDATE vehicle_loans SET vehicle_id=?, partner_id=?, loan_date=?, return_date=?, note=?, status=?, updated_at=NOW() WHERE id=?");
        $updateQuery->execute([$vehicle_id_new, $partner_id, $loan_date, $return_date, $note, $status, $id]);

        $_SESSION['success_message'] = "Peminjaman kendaraan <strong>" . htmlspecialchars($vehicle_id_new) . "</strong> berhasil diupdate.";
        header('Location: vehicle_loans.php');
        exit;
    }
}

$statuses = [
    'borrowed' => 'Dipinjam',
    'returned' => 'Dikembalikan',
];

include '../layout/header.php';
include '../layout/sidebar.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Peminjaman Kendaraan</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Kode Kendaraan</label>
                        <input type="text" name="vehicle_id" id="vehicle_id" class="form-control" value="<?= htmlspecialchars($data['vehicle_id']) ?>" readonly>
                        <input type="hidden" name="vehicle_id" value="<?= $data['vehicle_id'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="partner_name" class="form-label">Nama Partner</label>
                        <input type="text" id="partner_name" class="form-control" value="<?= htmlspecialchars($data['partner_name']) ?>" readonly>
                        <input type="hidden" name="partner_id" value="<?= $data['partner_id'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="loan_date" class="form-label">Waktu Pinjam</label>
                        <input type="datetime-local" name="loan_date" id="loan_date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['loan_date'])) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="return_date" class="form-label">Waktu Kembali</label>
                        <input type="datetime-local" name="return_date" id="return_date" class="form-control <?= $returnDateError ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['return_date'] ?? date('Y-m-d\TH:i', strtotime($data['return_date']))) ?>" required>
                        <?php if ($returnDateError): ?>
                            <div class="invalid-feedback"><?= $returnDateError ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan</label>
                        <textarea name="note" id="note" class="form-control" rows="5"><?= htmlspecialchars($data['note']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" style="color: black;">
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($data['status'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="vehicle_loans.php" class="btn btn-secondary mx-2" text-white>Kembali</a>
                </form>
            </div>
        </div>
        <?php include '../layout/footer.php'; ?>
    </div>
</div>