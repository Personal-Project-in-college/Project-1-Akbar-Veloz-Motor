<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

$id = $_GET['id'] ?? null;
// 🪢 Ambil id partner dari URL

if (!$id) {
    die("Id tidak ditemukan.");
}

// 🪢 Ambil data vehicle berdasarkan Id
$data = $koneksi->prepare("SELECT * FROM vehicles WHERE id = ? AND deleted_at IS NULL");
$data->execute([$id]);
$vehicle = $data->fetch(PDO::FETCH_ASSOC);

if (!$vehicle) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Vehicle tidak ditemukan.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $brand = $_POST['brand_model'];
    $type = $_POST['type_vehicle'];
    $color = $_POST['color'];
    $production = $_POST['production_year'];
    $serial = $_POST['serial_number'];
    $stnk = $_POST['stnk_deadline'];
    $kilometer = $_POST['kilometer'];
    $cc = $_POST['cc_engine'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $user = $_POST['user_id'];
    $branch = $_POST['branch_id'];
    
    // ⬇️ Update data ke database
    $data = $koneksi->prepare("UPDATE vehicles SET brand_model=?, type_vehicle=?, color=?, production_year=?, serial_number=?, stnk_deadline=?, kilometer=?, cc_engine=?, description=?, price=?, status=?, user_id=?, branch_id=?, deleted_by_branch_at=NULL WHERE id=?");
    $data->execute([$brand, $type, $color, $production, $serial, $stnk, $kilometer, $cc, $desc, $price, $status, $user, $branch, $id]);
    
    // 🚀 Balik ke halaman index
    header('Location: index.php');
    exit;
}

// 🔍 Ambil hanya cabang yang belum dihapus (soft delete = null)
$branches = $koneksi->query("SELECT * FROM branches WHERE deleted_at IS NULL")->fetchAll();
?>

<!-- 📅 Form edit data -->
<h2>Edit Data Kendaraan</h2>
<form method="POST">
    Kode vehicles: 
    <input type="text" name="id" value="<?= $vehicle['id'] ?>" disabled><br>
    
    Brand Model: 
    <input type="text" name="brand_model" value="<?= $vehicle['brand_model'] ?>"><br>
    
    Type:
    <select name="type_vehicle">
        <?php $types = ['motorcycle', 'car']; ?>
        <?php foreach ($types as $type): ?>
            <option value="<?= $type ?>" <?= $vehicle['type_vehicle'] == $type ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
        <?php endforeach ?>
    </select><br>
    
    Color: 
    <input type="text" name="color" value="<?= $vehicle['color'] ?>"><br>
    
    Production Year: 
    <input type="date" name="production_year" value="<?= $vehicle['production_year'] ?>"><br>
    
    Serial Number: 
    <input type="text" name="serial_number" value="<?= $vehicle['serial_number'] ?>"><br>
    
    STNK Deadline: 
    <input type="date" name="stnk_deadline" value="<?= $vehicle['stnk_deadline'] ?>"><br>
    
    Kilometer: 
    <input type="number" name="kilometer" value="<?= $vehicle['kilometer'] ?>"><br>
    
    CC Engine: 
    <input type="number" name="cc_engine" value="<?= $vehicle['cc_engine'] ?>"><br>
    
    Description: 
    <textarea name="description"><?= $vehicle['description'] ?></textarea><br>
    
    Price: 
    <input type="number" name="price" value="<?= $vehicle['price'] ?>"><br>
    
    Status:
    <select name="status">
        <?php $statuses = ['available', 'service', 'test_drive', 'sold']; ?>
        <?php foreach ($statuses as $status): ?>
            <option value="<?= $status ?>" <?= $vehicle['status'] == $status ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $status)) ?></option>
        <?php endforeach ?>
    </select><br>
    
    User Id: 
    <input type="number" name="user_id" value="<?= $vehicle['user_id'] ?>"><br>
    
    Branch:
    <select name="branch_id">
        <?php foreach ($branches as $branch): ?>
            <option value="<?= $branch['id'] ?>" <?= $vehicle['branch_id'] == $branch['id'] ? 'selected' : '' ?>><?= $branch['name'] ?></option>
        <?php endforeach ?>
    </select><br>
    
    <button type="submit">Update</button>
</form>
