<?php

/**
 * File: create.php
 * Halaman ini digunakan untuk menambahkan data cabang baru ke dalam sistem.
 * Pengguna akan mengisi form nama dan alamat cabang.
 * Slug akan digenerate secara otomatis berdasarkan nama cabang.
 */

// ------------------------------
// INISIALISASI & KONFIGURASI
// ------------------------------

// Memulai atau melanjutkan sesi yang sudah ada.
// Diperlukan untuk menggunakan variabel $_SESSION, misalnya untuk pesan notifikasi.
session_start();

// 1. Mengimpor file konfigurasi untuk koneksi ke database.
include '../../../../config/koneksi.php';

// 2. Memeriksa apakah pengguna sudah login.
// Jika belum, pengguna akan diarahkan ke halaman login.
include '../../../../helpers/functionCheckLogin.php';

checkLogin();
include '../../../../helpers/functionCheckRole.php';

// 3. Mengimpor fungsi untuk menghasilkan slug dari string (nama cabang).
include '../../../../helpers/functionGenerateSlug.php';

// 4. Memproses data form jika request method adalah POST (artinya form telah disubmit).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data 'name' dari form yang disubmit.
    $name = $_POST['name'];
    // Menghasilkan slug dari nama cabang menggunakan fungsi generateSlug.
    $slug = generateSlug($name);
    // Mengambil data 'address' dari form yang disubmit.
    $address = $_POST['address'];

    // Menyiapkan query SQL untuk memasukkan data cabang baru ke tabel 'branches'.
    // Menggunakan prepared statement untuk mencegah SQL injection.
    // 'NOW()' digunakan untuk mengisi kolom 'created_at' dengan timestamp saat ini.
    $insertQuery = $koneksi->prepare("INSERT INTO branches (name, slug, address, created_at) VALUES (?, ?, ?, NOW())");
    // Menjalankan query dengan data yang sudah diambil dan digenerate.
    $insertQuery->execute([$name, $slug, $address]);

    // Menyimpan pesan sukses ke dalam session untuk ditampilkan di halaman berikutnya.
    $_SESSION['success'] = "Cabang <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";

    // Mengarahkan pengguna kembali ke halaman utama data cabang (branch.php).
    header("Location: branch.php");
    // Menghentikan eksekusi skrip setelah redirect untuk memastikan tidak ada output lain.
    exit;
}

// 5. Mengimpor bagian layout header (HTML head, CSS, bagian atas halaman).
include '../layout/header.php';
// 6. Mengimpor bagian layout sidebar (menu navigasi samping).
include '../layout/sidebar.php';
?>

<?php if (hasAnyRole(['Owner'])) : ?>
    <!-- Main Content -->
    <div class="main-panel">
        <div class="content-wrapper">
            <h3 class="mb-4">Tambah Cabang</h3>

            <!-- Card Wrapper untuk Form -->
            <div class="card">
                <div class="card-body">
                    <!-- Form untuk menambah cabang baru, data dikirim menggunakan metode POST -->
                    <form method="POST" action="create.php">
                        <!-- Input untuk Nama Cabang -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Masukan Nama Cabang" required>
                        </div>

                        <!-- Input tersembunyi untuk slug.
                        Nilainya diisi oleh PHP dengan memanggil generateSlug.
                        Atribut 'disabled' berarti nilai ini tidak akan dikirim bersama form.
                        Slug yang disimpan ke database di-generate di sisi server (lihat blok PHP di atas),
                        yang merupakan pendekatan yang lebih aman dan andal.
                        Input ini mungkin untuk tujuan tampilan atau debugging di sisi klien jika diperlukan.
                    -->
                        <input type="hidden" name="slug-display" id="slug-display" value="<?= generateSlug($_POST['name'] ?? '') ?>" disabled>

                        <!-- Input untuk Alamat Cabang -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="5" placeholder="Masukan Alamat Lengkap Cabang" required></textarea>
                            <!-- Catatan: Atribut cols dan rows pada textarea bisa disesuaikan atau diatur via CSS -->
                        </div>

                        <!-- Tombol Aksi -->
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="branch.php" class="btn btn-secondary text-white mx-2">Kembali</a>
                    </form>
                </div>
            </div>

            <?php
            // 7. Mengimpor bagian layout footer (HTML penutup, script JS, dll).
            include '../layout/footer.php';
            ?>
        </div> <!-- content-wrapper ends -->
    </div>
    <!-- main-panel ends -->
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