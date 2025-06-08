<?php

/**
 * File: edit.php
 * Halaman ini digunakan untuk mengedit data cabang yang sudah ada.
 * Halaman ini mengambil data berdasarkan 'slug' dari URL, menampilkannya dalam form,
 * dan memproses pembaruan data saat form disubmit.
 */

// ------------------------------
// INISIALISASI & KONFIGURASI
// ------------------------------

// Memulai session untuk menggunakan variabel $_SESSION (untuk notifikasi).
session_start();

// 1. Mengimpor file-file yang diperlukan.
include '../../../../config/koneksi.php'; // Koneksi database
include '../../../../helpers/functionGenerateSlug.php'; // Fungsi untuk membuat slug
include '../../../../helpers/functionCheckLogin.php'; // Fungsi untuk memeriksa status login
checkLogin(); // Menjalankan pengecekan login
include '../../../../helpers/functionCheckRole.php';
// ------------------------------
// PENGAMBILAN DATA UNTUK FORM
// ------------------------------

// 2. Ambil 'slug' dari URL dengan aman. Jika tidak ada, nilainya akan null.
$slug = $_GET['slug'] ?? null;

// 3. Jika tidak ada 'slug' di URL, hentikan eksekusi dan tampilkan pesan.
if (!$slug) {
    die("Error: Slug cabang tidak ditemukan di URL.");
}

// 4. Ambil data cabang dari database berdasarkan slug.
// Hanya mengambil data yang aktif (deleted_at IS NULL).
$query = $koneksi->prepare("SELECT * FROM branches WHERE slug = ? AND deleted_at IS NULL");
$query->execute([$slug]);
$branch = $query->fetch(PDO::FETCH_ASSOC);

// 5. Jika data dengan slug tersebut tidak ditemukan, hentikan eksekusi.
if (!$branch) {
    die("Error: Data cabang tidak ditemukan atau sudah dihapus.");
}

// ------------------------------
// PEMROSESAN FORM UPDATE
// ------------------------------

// 6. Jika form disubmit (method adalah POST), proses data update.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data baru dari form.
    $name = $_POST['name'];
    $address = $_POST['address'];
    // Membuat slug baru berdasarkan nama yang mungkin juga baru.
    $newSlug = generateSlug($name);

    // Menyiapkan query UPDATE menggunakan prepared statement untuk keamanan.
    $updateQuery = $koneksi->prepare("UPDATE branches SET name = ?, slug = ?, address = ?, updated_at = NOW() WHERE id = ?");
    // Menjalankan query dengan data baru dan ID dari data cabang yang diedit.
    $updateQuery->execute([$name, $newSlug, $address, $branch['id']]);

    // Menyimpan pesan sukses ke session.
    $_SESSION['success'] = "Cabang <strong>" . htmlspecialchars($name) . "</strong> berhasil diupdate.";

    // Mengarahkan pengguna kembali ke halaman utama.
    header("Location: branch.php");
    exit;
}

// 7. Mengimpor layout header dan sidebar.
include '../layout/header.php';
include '../layout/sidebar.php';
?>

<?php if (hasAnyRole(['Owner'])) : ?>
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Edit Cabang</h3>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Masukan Nama" value="<?= htmlspecialchars($branch['name']) ?>" required>
                    </div>

                    <input type="hidden" name="slug-display" value="<?= htmlspecialchars($branch['slug']) ?>" disabled>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" name="address" rows="5" placeholder="Masukan Alamat" required><?= htmlspecialchars($branch['address']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="branch.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                </form>
            </div>
        </div>

        <?php
        // 8. Mengimpor bagian layout footer (termasuk tag penutup, script JS, dll).
        include '../layout/footer.php';
        ?>
    </div>
</div>
<?php endif ?>

<?php if (!hasAnyRole(['Owner'])) : ?>
<div class="main-panel">
    <div class="content-wrapper d-flex justify-content-center align-items-center">

        <h2 class="mb-4 text-danger"><u><strong>Hak Akses Khusus Owner !</strong></u></h2>

        <?php
        // Mengimpor bagian layout footer (termasuk tag penutup, script JS, dll).
        include '../layout/footer.php';
        ?>
    </div>
</div>
<?php endif ?>
