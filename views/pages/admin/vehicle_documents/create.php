<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

// 🪢 Ambil kendaraan yang belum ada di vehicle_documents dan vehicle tidak didelete dari branch
$vehicles = $koneksi->query("SELECT id FROM vehicles WHERE id NOT IN (SELECT vehicle_id FROM vehicle_documents) AND (deleted_at IS NULL AND deleted_by_branch_at IS NULL)");

// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $vehicles_id = $_POST['vehicle_id'];
    $stnk = $_POST['stnk'];
    $bpkp = $_POST['bpkb'];
    $service = $_POST['service_note'];
    $nota = $_POST['nota'];
    $asuransi = $_POST['asuransi'];

    // ⬇️ Simpan data ke database (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at)
    $data = $koneksi->prepare("INSERT INTO vehicle_documents (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $data->execute([$vehicles_id, $stnk, $bpkp, $service, $nota, $asuransi]);
    
    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}
?>
    
<h2>Tambah Dokumen Kendaraan</h2>
<form method="POST">
    Vehicle ID:
    <select name="vehicle_id" required>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['id'] ?>"><?= $vehicle['id'] ?></option>
        <?php endforeach; ?>
    </select><br>

    STNK: 
    <input type="text" name="stnk" required><br>
    
    BPKB: 
    <input type="text" name="bpkb" required><br>
    
    Nota Service: 
    <input type="text" name="service_note" required><br>
    
    Nota Pembelian: 
    <input type="text" name="nota" required><br>
    
    Asuransi: 
    <input type="text" name="asuransi" required><br>
    
    <button type="submit">Tambah</button>
</form>
