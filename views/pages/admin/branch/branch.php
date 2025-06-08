<?php

/**
 * File: branch.php
 * Halaman ini bertanggung jawab untuk menampilkan daftar data cabang (branches) yang aktif.
 * Fitur:
 * - Menampilkan data dalam tabel dengan pagination.
 * - Tab untuk beralih antara data aktif dan data yang sudah dihapus (soft delete).
 * - Tombol untuk menambah data baru.
 * - Kolom pencarian (hanya tampilan).
 * - Keamanan dengan memeriksa status login pengguna.
 */

// ------------------------------
// INISIALISASI & KONFIGURASI
// ------------------------------

// 1. Mengimpor file koneksi database.
include '../../../../config/koneksi.php';

// 2. Memeriksa apakah pengguna sudah login. Jika belum, akan diarahkan ke halaman login.
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

// 3. Mengimpor fungsi untuk menampilkan notifikasi/alert (misal: setelah berhasil menambah data).
include '../../../../helpers/functionShowAlert.php';

// 4. Mengimpor bagian layout header (termasuk tag <head>, CSS, dan bagian atas halaman).
include '../layout/header.php';

// 5. Mengimpor bagian layout sidebar (menu navigasi samping).
include '../layout/sidebar.php';

// Variabel untuk menandai halaman aktif di menu navigasi.
// Mengambil nama file saat ini (misal: "branch.php").
$activePage = basename($_SERVER['PHP_SELF']);
?>

<div class="main-panel">
    <div class="content-wrapper">

        <?php
        // Menjalankan fungsi untuk menampilkan alert jika ada.
        showAlert();
        ?>

        <h3 class="mb-4">Data Cabang</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <?php if (hasAnyRole(['Owner'])) : ?>
                <a href="create.php" class="btn btn-primary">Tambah</a>
            <?php endif ?>

            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'branch.php') ? 'active' : '' ?>" href="branch.php">Data Aktif</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'delete.php') ? 'active' : '' ?>" href="delete.php">Data Terhapus</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped" id="productTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // ------------------------------
                                // LOGIKA PENGAMBILAN & PENAMPILAN DATA
                                // ------------------------------

                                // A. Pengaturan Pagination
                                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Halaman saat ini, default ke 1 jika tidak ada.
                                $limit = 10; // Jumlah data yang ditampilkan per halaman.
                                $offset = ($page - 1) * $limit; // Menghitung offset untuk query database.

                                // B. Menghitung Total Data untuk Pagination
                                // Query ini menghitung jumlah total cabang yang aktif (belum di-soft delete).
                                $totalData = $koneksi->query("SELECT COUNT(*) FROM branches WHERE deleted_at IS NULL")->fetchColumn();
                                $totalPages = ceil($totalData / $limit); // Menghitung total jumlah halaman.

                                // C. Mengambil Data dari Database
                                // Menggunakan prepared statement untuk keamanan dari SQL Injection.
                                // Mengambil data cabang yang aktif, diurutkan berdasarkan tanggal dibuat, dengan limit dan offset.
                                $query = $koneksi->prepare("SELECT * FROM branches WHERE deleted_at IS NULL ORDER BY created_at ASC LIMIT :limit OFFSET :offset");
                                $query->bindValue(':limit', $limit, PDO::PARAM_INT);
                                $query->bindValue(':offset', $offset, PDO::PARAM_INT);
                                $query->execute();
                                $dataBranchs = $query->fetchAll(PDO::FETCH_ASSOC);

                                // D. Menampilkan Data ke Tabel
                                $no = $offset + 1; // Nomor urut dimulai dari offset + 1.

                                // Looping untuk setiap baris data yang diambil.
                                foreach ($dataBranchs as $row) {
                                    // Mengamankan output HTML dari XSS. Penting untuk data yang akan ditaruh di atribut HTML seperti 'title'.
                                    $address = htmlspecialchars($row['address']);
                                    // Memotong teks alamat jika lebih dari 30 karakter untuk tampilan di tabel.
                                    $shortAddress = substr($row['address'], 0, 30) . (strlen($row['address']) > 30 ? "..." : "");

                                    // Mencetak baris tabel (<tr>) untuk setiap data.
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['name']}</td>
                                            <td data-bs-toggle='tooltip' data-bs-placement='top' title='{$address}'>
                                                {$shortAddress}
                                            </td>
                                            <td style='display: flex; align-items: center; gap: 8px;'>
                                                <a href='edit.php?slug={$row['slug']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'>
                                                    <i class='mdi mdi-pencil'></i>
                                                </a>
                                                <a href='softDelete.php?id={$row['id']}' title='Delete' class='btn btn-danger btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; color: white; border-radius: 4px;'>
                                                    <i class='mdi mdi-delete-restore'></i>
                                                </a>
                                            </td>
                                        </tr>";
                                    $no++; // Increment nomor urut.
                                }
                                if (empty($dataBranchs)) {
                                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data branch yang aktif.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($totalPages > 1) : ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link bg-primary text-white" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link <?= ($page == $i) ? 'bg-primary text-white' : 'text-primary bg-white' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link bg-primary text-white" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <script>
            // Inisialisasi semua tooltip Bootstrap yang ada di halaman.
            // Diperlukan agar tooltip pada kolom alamat dapat berfungsi.
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        </script>

        <?php
        // Mengimpor bagian layout footer (termasuk tag penutup, script JS, dll).
        include '../layout/footer.php';
        ?>
    </div>
</div>