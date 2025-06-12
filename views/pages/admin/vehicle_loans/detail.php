<?php

session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID peminjaman tidak ditemukan.";
    header('Location: vehicle_loans.php');
    exit;
}

$id = $_GET['id'];

// Ambil data peminjaman berdasarkan ID
$loan = $koneksi->prepare("
    SELECT vehicle_loans.*, partners.name AS partner_name 
    FROM vehicle_loans 
    LEFT JOIN partners ON vehicle_loans.partner_id = partners.id 
    WHERE vehicle_loans.id = ?
");

$loan->execute([$id]);
$data = $loan->fetch();

if (!$data) {
    $_SESSION['error'] = "Data peminjaman tidak ditemukan.";
    header('Location: vehicle_loans.php');
    exit;
}

// Ambil semua kendaraan
$vehicles = $koneksi->query("SELECT id FROM vehicles WHERE deleted_at IS NULL AND deleted_by_branch_at IS NULL");

// Ambil semua partner
$partners = $koneksi->query("SELECT * FROM partners WHERE deleted_at IS NULL");

$statuses = [
    'borrowed' => 'Dipinjam',
    'returned' => 'Dikembalikan',
];

include '../layout/header.php';
include '../layout/sidebar.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Detail Peminjaman Kendaraan</h3>
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Kode Kendaraan</label>
                        <input type="text" name="vehicle_id" id="vehicle_id" class="form-control" value="<?= htmlspecialchars($data['vehicle_id']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="partner_name" class="form-label">Nama Partner</label>
                        <input type="text" id="partner_name" class="form-control" value="<?= htmlspecialchars($data['partner_name']) ?>" disabled>

                        <!-- Hidden untuk simpan partner_id -->
                        <input type="hidden" name="partner_id" value="<?= $data['partner_id'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="loan_date" class="form-label">Waktu Pinjam</label>
                        <input type="datetime-local" name="loan_date" id="loan_date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['loan_date'])) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="return_date" class="form-label">Waktu Kembali</label>
                        <input type="datetime-local" name="return_date" id="return_date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['return_date'])) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan</label>
                        <textarea name="note" id="note" class="form-control" rows="5" disabled><?= htmlspecialchars($data['note']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" style="color: black;" disabled>
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($data['status'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <a href="vehicle_loans.php" class="btn btn-secondary text-white">Kembali</a>
                </form>
            </div>
        </div>
        <?php include '../layout/footer.php'; ?>
    </div>
</div>