<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../helpers/functionGenerateSlug.php';
// 🔗 Hubungin ke function Generate Slug

$slug = $_GET['slug'] ?? null;
// 🪢 Ambil Slug partner dari URL

if (!$slug) {
    // ❗ Kalau gak ada Slug di URL
    die("Slug tidak ditemukan.");
}

// 🪢 Ambil data partner berdasarkan Slug
$data = $koneksi->prepare("SELECT * FROM partners WHERE slug = ? AND deleted_at IS NULL");
$data->execute([$slug]);
$partner = $data->fetch(PDO::FETCH_ASSOC);

if (!$partner) {
    // ❗ Kalau datanya gak ditemukan
    die("Data partner tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $name = $_POST['name'];
    $newSlug = generateSlug($name);
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // ⬇️ Update data ke database
    $data = $koneksi->prepare("UPDATE partners SET name = ?, slug = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$name, $newSlug, $phone, $address, $partner['id']]);

    // 🚀 Balik ke halaman index
    header('Location: index.php');
    exit;
}
?>

<!-- 📅 Form edit data -->
<h2>Edit Partner</h2>
<form method="POST">
    Nama Partner: 
    <!-- 🛡️ htmlspecialchars() : Biar isi form aman dari karakter aneh atau XSS -->
    <input type="text" name="name" value="<?= htmlspecialchars($partner['name']) ?>" required><br>

    <!-- 🧬 Tampilkan slug baru, dibikin otomatis dari nama -->
    <p>Slug Otomatis: <b><?= generateSlug($_POST['name'] ?? $partner['name']) ?></b></p>

    Telepon: 
    <input type="number" name="phone" value="<?= $partner['phone'] ?>" required><br>

    Alamat: 
    <textarea name="address" required><?= htmlspecialchars($cabang['address']) ?></textarea><br>
    
    <button type="submit">Update</button>
</form>