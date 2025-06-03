<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../helpers/functionGenerateSlug.php';
// 🔗 Hubungin ke function Generate Slug
include '../../../../helpers/functionHashPassword.php';
// 🔗 Hubungin ke function Hashing Password

$slug = $_GET['slug'] ?? null;
// 🪢 Ambil slug users dari URL

if (!$slug) {
    die("Slug tidak ditemukan.");
}

// 🪢 Ambil data user berdasarkan Slug
$data = $koneksi->prepare("SELECT * FROM users WHERE slug = ? AND deleted_at IS NULL");
$data->execute([$slug]);
$user = $data->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Vehicle tidak ditemukan.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $name = $_POST['name'];
    $slug = generateSlug($name); //  🧬 Bikin slug dari nama cabang
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = hashPassword($_POST['password']); // 🔐 Hash password
    $role_id = $_POST['role_id'];
    
    // ⬇️ Update data ke database
    $data = $koneksi->prepare("UPDATE users SET name = ?, slug = ?, phone = ?, address = ?, username = ?, password = ?, role_id = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$name, $slug, $phone, $address, $username, $password, $role_id, $user['id']]);
    
    // 🚀 Balik ke halaman index
    header('Location: index.php');
    exit;
}

// 🔍 Ambil hanya role yang belum dihapus (soft delete = null)
$roles = $koneksi->query("SELECT * FROM roles WHERE deleted_at IS NULL")->fetchAll();
?>

<!-- 📅 Form edit data -->
<h2>Edit Data User</h2>
<form method="POST">
    Nama User:
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>

    <!-- 🧬 Slug ini sebenernya gak perlu ditampilin karena dibikin otomatis, jadi disembunyikan -->
    <input type="hidden" name="slug" value="<?= generateSlug($_POST['name'] ?? '') ?>" disabled><br>

    Telepon:
    <input type="text" name="phone" value="<?= $user['phone'] ?>" required type="number"><br>

    Alamat:
    <textarea name="address" required><?= htmlspecialchars($user['address']) ?></textarea><br>
    
    Username:
    <input name="username" value="<?= $user['username'] ?>" required></input><br>

    Password:
    <input name="password" type="password"></input><br>

    Role:
    <select name="role_id" required>
        <?php foreach ($roles as $role): ?>
            <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>><?= $role['name'] ?></option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Update</button>
</form>
