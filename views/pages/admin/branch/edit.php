<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../helpers/functionGenerateSlug.php';
// 🔗 Hubungin ke function Generate Slug

$slug = $_GET['slug'] ?? null;
// 🪢 Ambil Slug cabang dari URL

if (!$slug) {
    // ❗ Kalau gak ada Slug di URL
    die("Slug tidak ditemukan.");
}

// 🪢 Ambil data cabang berdasarkan Slug
$data = $koneksi->prepare("SELECT * FROM branches WHERE slug = ? AND deleted_at IS NULL");
$data->execute([$slug]);
$cabang = $data->fetch(PDO::FETCH_ASSOC);

if (!$cabang) {
    // ❗ Kalau datanya gak ditemukan
    die("Data cabang tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $name = $_POST['name'];
    $newSlug = generateSlug($name);
    $address = $_POST['address'];

    // ⬇️ Update data ke database
    $data = $koneksi->prepare("UPDATE branches SET name = ?, slug = ?, address = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$name, $newSlug, $address, $cabang['id']]);

    // 🚀 Balik ke halaman index
    header("Location: index.php");
    exit;
}
?>

<!-- 📅 Form edit data -->
<h2>Edit Data Cabang</h2>
<form method="POST">
    Nama Cabang:
    <!-- 🛡️ htmlspecialchars() : Biar isi form aman dari karakter aneh atau XSS -->
    <input type="text" name="name" value="<?= htmlspecialchars($cabang['name']) ?>" required><br>

    <!-- 🧬 Tampilkan slug baru, dibikin otomatis dari nama -->
    <p>Slug Otomatis: <b><?= generateSlug($_POST['name'] ?? $cabang['name']) ?></b></p>

    Alamat:
    <textarea name="address" required><?= htmlspecialchars($cabang['address']) ?></textarea><br>

    <button type="submit">Update</button>
</form>