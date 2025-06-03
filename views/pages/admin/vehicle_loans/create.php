<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

// 🪢 Ambil kendaraan yang belum ada di vehicle_documents
$vehicles = $koneksi->query("SELECT id FROM vehicles WHERE deleted_at IS NULL AND deleted_by_branch_at IS NULL");
$partners = $koneksi->query("SELECT * FROM partners WHERE deleted_at IS NULL");

// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $vehicles_id = $_POST['vehicle_id'];
    $partners_id = $_POST['partner_id'];
    $users_id = $_POST['user_id'];
    $loan_date = $_POST['loan_date'];
    $return_date = $_POST['return_date'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    // ⬇️ Simpan data ke database (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at)
    $data = $koneksi->prepare("INSERT INTO vehicle_loans (vehicle_id, partner_id, user_id, loan_date, return_date, reason, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $data->execute([$vehicles_id, $partners_id, $users_id, $loan_date, $return_date, $reason]);
    
    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}
?>
    
<h2>Tambah Peminjaman Kendaraan</h2>
<form method="POST">
    Vehicle ID:
    <select name="vehicle_id" required>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['id'] ?>"><?= $vehicle['id'] ?></option>
        <?php endforeach; ?>
    </select><br>

    Partner ID:
    <select name="partner_id" required>
        <?php foreach ($partners as $partner): ?>
            <option value="<?= $partner['id'] ?>"><?= $partner['name'] ?></option>
        <?php endforeach; ?>
    </select><br>

    User Id: 
    <input type="number" name="user_id" value="<?= $data['user_id'] ?? '' ?>"><br>
    
    Loan Date: 
    <input type="date" name="loan_date" required><br>

    Return Date: 
    <input type="date" name="return_date" required><br>
    
    Reason: 
    <input type="text" name="reason" required><br>
    
    Status :
    <select name="status">
        <?php $statuses = ['borrowed', 'returned']; ?>
        <?php foreach ($statuses as $status): ?>
            <option value="<?=$status?>" <?=($data['status'] ?? '') == $status ? 'selected' : ''?>><?=ucwords(str_replace('_', ' ', $status))?></option>
        <?php endforeach ?>
    </select><br>
    
    <button type="submit">Tambah</button>
</form>
