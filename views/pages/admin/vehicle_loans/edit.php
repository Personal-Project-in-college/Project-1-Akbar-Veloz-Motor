<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

$id = $_GET['id'];
// 🪢 Ambil Id vehicle document dari URL

if (!$id) {
    // ❗ Kalau gak ada Id di URL
    die("Id tidak ditemukan.");
}

// 🪢 Ambil data vehicle document berdasarkan Slug
$data = $koneksi->prepare("SELECT * FROM vehicle_loans WHERE id = ? AND deleted_at IS NULL");
$data->execute([$id]);
$vehicle_loans = $data->fetch();

if (!$vehicle_loans) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Vehicle Dokumen tidak ditemukan.");
}

// Handle update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $vehicles_id = $_POST['vehicle_id'];
    $partners_id = $_POST['partner_id'];
    $users_id = $_POST['user_id'];
    $loan_date = $_POST['loan_date'];
    $return_date = $_POST['return_date'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    // ⬇️ Update data ke database
    $data = $koneksi->prepare("UPDATE vehicle_loans SET vehicle_id = ?, partner_id = ?, user_id = ?, loan_date = ?, return_date = ?, reason = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$vehicles_id, $partners_id, $users_id, $loan_date, $return_date, $reason, $status, $id]);

    // 🚀 Balik ke halaman index
    header('Location: index.php');
    exit;
}
?>

<!-- 📅 Form edit data -->
<h2>Edit Peminjaman Kendaraan</h2>
<form method="POST">
    Vehicle ID: 
    <input type="text" name="vehicle_id" value="<?= $vehicle_loans['vehicle_id'] ?>"><br>
    
    Partner ID: 
    <input type="text" name="partner_id" value="<?= $vehicle_loans['partner_id'] ?>"><br>

    User ID: 
    <input type="text" name="user_id" value="<?= $vehicle_loans['user_id'] ?>"><br>

    Loan Dete: 
    <input type="text" name="loan_date" value="<?= $vehicle_loans['loan_date'] ?>" required><br>
    
    Return Date: 
    <input type="text" name="return_date" value="<?= $vehicle_loans['return_date'] ?>" required><br>
    
    Reason: 
    <input type="text" name="reason" value="<?= $vehicle_loans['reason'] ?>" required><br>
    
    Status:
    <select name="status">
        <?php $statuses = ['borrowed', 'returned']; ?>
        <?php foreach ($statuses as $status): ?>
            <option value="<?= $status ?>" <?= $vehicle_loans['status'] == $status ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $status)) ?></option>
        <?php endforeach ?>
    </select><br>
    
    
    
    <button type="submit">Update</button>
</form>