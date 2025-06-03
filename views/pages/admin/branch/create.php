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
    $address = $_POST['address'];

    // ⬇️ Simpan data ke database (name, slug, address, created_at)
    $data = $koneksi->prepare("INSERT INTO branches (name, slug, address, created_at) VALUES (?, ?, ?, NOW())");
    $data->execute([$name, $slug, $address]);

    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}
?>

<!-- 📅 Form input untuk tambah data -->
<h2>Tambah Data Cabang</h2>
<form method="POST">
    Nama Cabang: <input type="text" name="name" required><br>

    <!-- 🧬 Slug ini sebenernya gak perlu ditampilin karena dibikin otomatis, jadi disembunyikan -->
    <input type="hidden" name="slug" value="<?= generateSlug($_POST['name'] ?? '') ?>" disabled><br>

    Alamat: 
    <textarea name="address" required></textarea><br>
    
    <button type="submit">Tambah</button>
</form>