<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../helpers/functionGenerateSlug.php';
// 🔗 Hubungin ke function Generate Slug
include '../../../../helpers/functionHashPassword.php';
// 🔗 Hubungin ke function Hashing Password


// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $name = $_POST['name'];
    $slug = generateSlug($name); //  🧬 Bikin slug dari nama user
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = hashPassword($_POST['password']); // 🔐 Hash password
    $role_id = $_POST['role_id'];

    // ⬇️ Simpan data ke database (name, slug, phone, address, username, password, role_id, created_at)
    $data = $koneksi->prepare("INSERT INTO users (name, slug, phone, address, username, password, role_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $data->execute([$name, $slug, $phone, $address, $username, $password, $role_id]);

    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}

// 🔍 Ambil hanya role yang belum dihapus (soft delete = null)
$roles = $koneksi->query("SELECT * FROM roles WHERE deleted_at IS NULL")->fetchAll();

?>

<!-- 📅 Form input untuk tambah data -->
<h2>Tambah Data User</h2>
<form method="POST">
    Nama User:
    <input type="text" name="name" required><br>

    <!-- 🧬 Slug ini sebenernya gak perlu ditampilin karena dibikin otomatis, jadi disembunyikan -->
    <input type="hidden" name="slug" value="<?= generateSlug($_POST['name'] ?? '') ?>" disabled><br>

    Telepon:
    <input type="text" name="phone" required type="number"><br>

    Alamat:
    <textarea name="address" required></textarea><br>
    
    Username:
    <input name="username" required></input><br>

    Password:
    <input name="password" required type="password"></input><br>

    Role:
    <select name="role_id" required>
        <?php foreach ($roles as $role): ?>
            <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Tambah</button>
</form>