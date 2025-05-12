<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $id = $_POST['id'];
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

    // ⬇️ Simpan data ke database (id, brand_model, type_vehicle, color, production_year, serial_number, stnk_deadline, kilometer, cc_engine, description, price, status, user_id, branch_id, created_at)
    $data = $koneksi->prepare("INSERT INTO vehicles (id, brand_model, type_vehicle, color, production_year, serial_number, stnk_deadline, kilometer, cc_engine, description, price, status, user_id, branch_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $data->execute([$id, $brand, $type, $color, $production, $serial, $stnk, $kilometer, $cc, $desc, $price, $status, $user, $branch]);
    
    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}

// 🔍 Ambil hanya cabang yang belum dihapus (soft delete = null)
$branches = $koneksi->query("SELECT * FROM branches WHERE deleted_at IS NULL")->fetchAll();

?>

<!-- 📅 Form input untuk tambah data -->
<h2>Tambah Data Kendaraan</h2>
<form method="POST">
    Kode vehicles: 
    <input type="text" name="id" value="<?= $data['id'] ?? '' ?>"><br>
    
    Brand Model: 
    <input type="text" name="brand_model" value="<?= $data['brand_model'] ?? '' ?>"><br>
    
    Type:
    <select name="type_vehicle">
        <?php $types = ['motorcycle', 'car']; ?>
        <?php foreach ($types as $type): ?>
            <option value="<?=$type?>" <?=($data['type_vehicle'] ?? '') == $type ? 'selected' : ''?>><?=ucfirst($type)?></option>
        <?php endforeach ?>
    </select><br>

    Color: 
    <input type="text" name="color" value="<?= $data['color'] ?? '' ?>"><br>
    
    Production Year: 
    <input type="date" name="production_year" value="<?= $data['production_year'] ?? '' ?>"><br>
    
    Serial Number: 
    <input type="text" name="serial_number" value="<?= $data['serial_number'] ?? '' ?>"><br>
    
    STNK Deadline: 
    <input type="date" name="stnk_deadline" value="<?= $data['stnk_deadline'] ?? '' ?>"><br>
    
    Kilometer: 
    <input type="number" name="kilometer" value="<?= $data['kilometer'] ?? '' ?>"><br>
    
    CC Engine: 
    <input type="number" name="cc_engine" value="<?= $data['cc_engine'] ?? '' ?>"><br>
    
    Description: 
    <textarea name="description"><?= $data['description'] ?? '' ?></textarea><br>
    
    Price: 
    <input type="number" name="price" value="<?= $data['price'] ?? '' ?>"><br>
    
    Status:
    <select name="status">
        <?php $statuses = ['available', 'service', 'test_drive', 'sold']; ?>
        <?php foreach ($statuses as $status): ?>
            <option value="<?=$status?>" <?=($data['status'] ?? '') == $status ? 'selected' : ''?>><?=ucwords(str_replace('_', ' ', $status))?></option>
        <?php endforeach ?>
    </select><br>
    
    User Id: 
    <input type="number" name="user_id" value="<?= $data['user_id'] ?? '' ?>"><br>
    
    Branch:
    <select name="branch_id">
        <?php foreach ($branches as $branch): ?>
            <option value="<?=$branch['id']?>" <?=($data['branch_id'] ?? '') == $branch['id'] ? 'selected' : ''?>><?=$branch['name']?></option>
        <?php endforeach?>
    </select><br>
    
    <button type="submit">Tambah</button>
</form>
