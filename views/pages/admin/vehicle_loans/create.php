<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

// Ambil kendaraan available
$vehicles = $koneksi->query("SELECT id FROM vehicles WHERE deleted_at IS NULL AND deleted_by_branch_at IS NULL AND status = 'available'");

// Ambil partner yang belum pernah meminjam atau sudah mengembalikan
$partners = $koneksi->query("SELECT * FROM partners WHERE deleted_at IS NULL AND (id NOT IN (SELECT partner_id FROM vehicle_loans) OR id IN (SELECT partner_id FROM vehicle_loans WHERE status = 'returned'))");

$returnDateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicles_id = $_POST['vehicle_id'];
    $partners_id = $_POST['partner_id'];
    $users_id = $_SESSION['user_id'];
    $loan_date = $_POST['loan_date'];
    $return_date = $_POST['return_date'];
    $note = $_POST['note'];
    $status = $_POST['status'];

    $loanTimestamp = strtotime($loan_date);
    $returnTimestamp = strtotime($return_date);

    // Validasi
    if ($returnTimestamp <= $loanTimestamp) {
        $returnDateError = "Waktu pengembalian tidak boleh sebelum waktu pinjam.";
    } elseif (($returnTimestamp - $loanTimestamp) > (7 * 24 * 60 * 60)) {
        $returnDateError = "Maksimal waktu peminjaman adalah 7 hari.";
    }

    // Kalau tidak ada error, baru simpan
    if ($returnDateError === '') {
        $insertVehicleLoanQuery = $koneksi->prepare("INSERT INTO vehicle_loans (vehicle_id, partner_id, user_id, loan_date, return_date, note, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $insertVehicleLoanQuery->execute([$vehicles_id, $partners_id, $users_id, $loan_date, $return_date, $note, $status]);

        $updateVehicleStatusQuery = $koneksi->prepare("UPDATE vehicles SET status = 'on_loan' WHERE id = ?");
        $updateVehicleStatusQuery->execute([$vehicles_id]);

        $_SESSION['success_message'] = "Peminjaman kendaraan <strong>" . htmlspecialchars($vehicles_id) . "</strong> berhasil ditambahkan.";
        header('Location: vehicle_loans.php');
        exit;
    }
}

$statuses = [
    'borrowed' => 'Dipinjam',
    'returned' => 'Dikembalikan',
];

$now = date('Y-m-d\TH:i'); // format datetime-local
$next24Hours = date('Y-m-d\TH:i', strtotime('+24 hours'));

// 5. Mengimpor bagian layout header (HTML head, CSS, bagian atas halaman).
include '../layout/header.php';
// 6. Mengimpor bagian layout sidebar (menu navigasi samping).
include '../layout/sidebar.php';
?>


<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Tambah Peminjaman Kendaraan</h3>

        <!-- Card Wrapper untuk Form -->
        <div class="card">
            <div class="card-body">
                <!-- Form untuk menambah cabang baru, data dikirim menggunakan metode POST -->
                <form method="POST" action="create.php" enctype="multipart/form-data">
                    <!-- Input untuk Nama Cabang -->
                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Kode Kendaraan</label>
                        <select class="form-select" id="vehicle_id" name="vehicle_id" required style="color: black;">
                            <?php if ($vehicles->rowCount() > 0): ?>
                                <?php foreach ($vehicles as $vehicle): ?>
                                    <option value="<?= $vehicle['id'] ?>"><?= $vehicle['id'] ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled selected>Data kosong</option>
                            <?php endif; ?>
                        </select>

                    </div>

                    <div class="mb-3">
                        <label for="partner_id" class="form-label">Nama Partner</label>
                        <select class="form-select" id="partner_id" name="partner_id" required style="color: black;">
                            <?php if ($partners->rowCount() > 0): ?>
                                <?php foreach ($partners as $partner): ?>
                                    <option value="<?= $partner['id'] ?>"><?= $partner['name'] ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled selected>Data kosong</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="loan_date" class="form-label">Waktu Pinjam</label>
                        <input type="datetime-local" class="form-control" id="loan_date" name="loan_date" value="<?= htmlspecialchars($_POST['loan_date'] ?? $now) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="return_date" class="form-label">Waktu Dikembalikan</label>
                        <input type="datetime-local" class="form-control <?= $returnDateError ? 'is-invalid' : '' ?>" id="return_date" name="return_date" value="<?= htmlspecialchars($_POST['return_date'] ?? $next24Hours) ?>" required>
                        <?php if ($returnDateError): ?>
                            <div class="invalid-feedback"><?= $returnDateError ?></div>
                        <?php endif; ?>
                    </div>


                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan Hasil Peminjaman</label>
                        <textarea class="form-control" id="note" name="note" rows="5" placeholder="Masukan Note"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" style="color: black;">
                            <?php foreach ($statuses as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($data['status'] ?? '') == $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="vehicle_loans.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php
        // 7. Mengimpor bagian layout footer (HTML penutup, script JS, dll).
        include '../layout/footer.php';
        ?>
    </div> <!-- content-wrapper ends -->
</div>
<!-- main-panel ends -->