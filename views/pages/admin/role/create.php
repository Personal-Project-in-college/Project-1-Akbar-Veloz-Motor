<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $name = $_POST['name'];

    // ⬇️ Simpan data ke database (name, created_at)
    $data = $koneksi->prepare("INSERT INTO roles (name, created_at) VALUES (?, NOW())");
    $data->execute([$name]);

    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}
?>

<!-- 📅 Form input untuk tambah data -->
<h2>Tambah Data Role</h2>
<form method="POST">
    Nama Role: 
    <input type="text" name="name" required><br>
    
    <button type="submit">Tambah</button>
</form>