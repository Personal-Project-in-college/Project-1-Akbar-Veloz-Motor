<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database

$id = $_GET['id'] ?? null;
// 🪢 Ambil Id role dari URL

if (!$id) {
    // ❗ Kalau gak ada Id di URL
    die("Id tidak ditemukan.");
}

// 🪢 Ambil data role berdasarkan Id
$data = $koneksi->prepare("SELECT * FROM roles WHERE id = ? AND deleted_at IS NULL");
$data->execute([$id]);
$role = $data->fetch(PDO::FETCH_ASSOC);

if (!$role) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Role tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $name = $_POST['name'];

    // ⬇️ Update data ke database
    $data = $koneksi->prepare("UPDATE roles SET name = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$name, $role['id']]);

    // 🚀 Balik ke halaman index
    header("Location: index.php");
    exit;
}
?>

<!-- 📅 Form edit data -->
<h2>Edit Data Role</h2>
<form method="POST">
    Nama Role:
    <!-- 🛡️ htmlspecialchars() : Biar isi form aman dari karakter aneh atau XSS -->
    <input type="text" name="name" value="<?= htmlspecialchars($role['name']) ?>" required><br>

    <button type="submit">Update</button>
</form>