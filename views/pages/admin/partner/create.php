<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../helpers/functionGenerateSlug.php'; 
// 🔗 Hubungin ke function Generate Slug

// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $name = $_POST['name'];
    $slug = generateSlug($name); //  🧬 Bikin slug dari nama cabang
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // ⬇️ Simpan data ke database (name, slug, phone, address, created_at)
    $data = $koneksi->prepare("INSERT INTO partners (name, slug, phone, address, created_at) VALUES (?, ?, ?, ?, NOW())");
    $data->execute([$name, $slug, $phone, $address]);

    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}
?>

<h2>Tambah Partner</h2>
<form method="POST">
    Nama Partner: 
    <input type="text" name="name" required><br>
    
    <!-- 🧬 Slug ini sebenernya gak perlu ditampilin karena dibikin otomatis, jadi disembunyikan -->
    <input type="hidden" name="slug" value="<?= generateSlug($_POST['name'] ?? '') ?>" disabled><br>
    
    Telepon: 
    <input type="text" name="phone" required><br>

    Alamat: 
    <textarea name="address" required></textarea><br>

    <button type="submit">Tambah</button>
</form>
