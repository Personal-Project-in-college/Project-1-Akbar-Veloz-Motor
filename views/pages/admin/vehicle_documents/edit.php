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
$data = $koneksi->prepare("SELECT * FROM vehicle_documents WHERE id = ? AND deleted_at IS NULL");
$data->execute([$id]);
$vehicle_document = $data->fetch();

if (!$vehicle_document) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Vehicle Dokumen tidak ditemukan.");
}

// Handle update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $stnk = $_POST['stnk'];
    $bpkp = $_POST['bpkb'];
    $service = $_POST['service_note'];
    $nota = $_POST['nota'];
    $asuransi = $_POST['asuransi'];

    // ⬇️ Update data ke database
    $data = $koneksi->prepare("UPDATE vehicle_documents SET stnk = ?, bpkb = ?, service_note = ?, nota = ?, asuransi = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$stnk, $bpkp, $service, $nota, $asuransi, $id]);

    // 🚀 Balik ke halaman index
    header('Location: index.php');
    exit;
}
?>

<!-- 📅 Form edit data -->
<h2>Edit Dokumen Kendaraan</h2>
<form method="POST">
    Vehicle ID: 
    <input type="text" value="<?= $vehicle_document['vehicle_id'] ?>" disabled><br>
    
    STNK: 
    <input type="text" name="stnk" value="<?= $vehicle_document['stnk'] ?>" required><br>
    
    BPKB: 
    <input type="text" name="bpkb" value="<?= $vehicle_document['bpkb'] ?>" required><br>
    
    Nota Service: 
    <input type="text" name="service_note" value="<?= $vehicle_document['service_note'] ?>" required><br>
    
    Nota Pembelian: 
    <input type="text" name="nota" value="<?= $vehicle_document['nota'] ?>" required><br>
    
    Asuransi: 
    <input type="text" name="asuransi" value="<?= $vehicle_document['asuransi'] ?>" required><br>
    
    <button type="submit">Update</button>
</form>