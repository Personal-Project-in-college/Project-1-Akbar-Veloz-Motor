<?php

/**
 * File: vehicles.php
 * Halaman utama untuk menampilkan daftar semua kendaraan yang aktif dalam sistem.
 * Dilengkapi dengan fitur pagination, tombol tambah, tab data aktif/terhapus,
 * serta detail seperti STNK dan status kendaraan.
 */

// 1. Inisialisasi dan Konfigurasi Dasar
// Bagian ini mencakup file-file konfigurasi, helper, dan pengecekan sesi login.
include '../../../../config/koneksi.php';              // Menghubungkan ke konfigurasi database.
include '../../../../helpers/functionCheckLogin.php'; // Memuat fungsi untuk memeriksa status login pengguna.
checkLogin();                                         // Menjalankan fungsi untuk memastikan pengguna sudah login sebelum mengakses halaman.
include '../../../../helpers/functionShowAlert.php';  // Memuat fungsi untuk menampilkan notifikasi atau pesan alert.
include '../layout/header.php';                       // Memuat bagian header dari tata letak halaman.
include '../layout/sidebar.php';                      // Memuat bagian sidebar dari tata letak halaman.

// Mengambil nama file saat ini dari URL.
// Ini digunakan untuk menandai tab navigasi mana yang sedang aktif di halaman.
$activePage = basename($_SERVER['PHP_SELF']);
?>

<div class="main-panel">
    <div class="content-wrapper">

        <?php
        // Menjalankan fungsi showAlert() untuk menampilkan pesan notifikasi (jika ada)
        // yang mungkin disimpan dalam session (misalnya, setelah operasi tambah/edit/hapus data berhasil).
        showAlert();
        ?>

        <h3 class="mb-4">Data Kendaraan</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <a href="create.php" class="btn btn-primary">Tambah</a>

            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'vehicles.php') ? 'active' : '' ?>" href="vehicles.php">Data Aktif</a>
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
                                    <th>Kode</th>
                                    <th>Brand Model</th>
                                    <th>Type Vehicle</th>
                                    <th>STNK Deadline</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // A. Pengaturan Pagination
                                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Mendapatkan nomor halaman saat ini dari URL, default-nya halaman 1.
                                $limit = 10; // Menentukan jumlah data (kendaraan) yang akan ditampilkan per halaman.
                                $offset = ($page - 1) * $limit; // Menghitung offset (mulai dari data ke berapa) untuk query database.

                                // B. Menghitung Total Data untuk Pagination
                                // Jika paginasi dimaksudkan untuk data kendaraan, query COUNT(*) ini seharusnya merujuk ke tabel 'vehicles'
                                // dengan kondisi WHERE yang sesuai (misal: WHERE deleted_at IS NULL).
                                $totalData = $koneksi->query("SELECT COUNT(*) FROM vehicles WHERE deleted_at IS NULL AND deleted_by_branch_at IS NULL")->fetchColumn();
                                var_dump($totalData);
                                $totalPages = ceil($totalData / $limit); // Menghitung total jumlah halaman yang dibutuhkan, dibulatkan ke atas.

                                // C. Mengambil Data Kendaraan dari Database
                                // Menggunakan prepared statement (prepare, bindValue, execute) untuk mencegah SQL Injection.
                                // Query ini mengambil semua kolom dari 'vehicles' dan nama cabang dari 'branches'
                                // dengan menggabungkan (LEFT JOIN) kedua tabel berdasarkan branch_id.
                                $query = $koneksi->prepare("SELECT vehicles.*, branches.name AS branch_name FROM vehicles LEFT JOIN branches ON vehicles.branch_id = branches.id WHERE vehicles.deleted_at IS NULL AND deleted_by_branch_at IS NULL ORDER BY vehicles.id ASC LIMIT :limit OFFSET :offset");

                                $query->bindValue(':limit', $limit, PDO::PARAM_INT); // Mengikat variabel $limit ke placeholder :limit.
                                $query->bindValue(':offset', $offset, PDO::PARAM_INT); // Mengikat variabel $offset ke placeholder :offset.
                                $query->execute(); // Menjalankan query.
                                $dataVehicles = $query->fetchAll(PDO::FETCH_ASSOC); // Mengambil semua hasil query sebagai array asosiatif.

                                // D. Menampilkan Data ke Tabel
                                $no = $offset + 1; // Inisialisasi nomor urut, tapi tidak digunakan di dalam loop di bawah.

                                // ♾️ Melakukan perulangan (loop) untuk setiap data kendaraan yang didapat dari database.
                                foreach ($dataVehicles as $row) {
                                    // 🔤 Mengubah nilai 'type_vehicle' menjadi format yang lebih mudah dibaca (Motor/Mobil).
                                    $typeVehicle = $row['type_vehicle'] === 'motorcycle' ? 'Motor' : 'Mobil';

                                    // 🏷️ Menentukan teks dan warna untuk label status berdasarkan nilai dari database.
                                    switch ($row['status']) {
                                        case 'available':
                                            $statusText = 'Tersedia';
                                            $statusColor = '#7B8255';
                                            break;
                                        case 'service':
                                            $statusText = 'Service';
                                            $statusColor = '#FA7D09';
                                            break;
                                        case 'test_drive':
                                            $statusText = 'Tes Jalan';
                                            $statusColor = '#838ABF';
                                            break;
                                        case 'sold':
                                            $statusText = 'Terjual';
                                            $statusColor = '#D29A18';
                                            break;
                                        default: // Jika status tidak dikenali, tampilkan apa adanya.
                                            $statusText = ucfirst($row['status']);
                                            $statusColor = '#6c757d';
                                            break;
                                    }

                                    // Perhitungan sisa waktu STNK
                                    $stnkText = 'Data Tidak Valid';
                                    $stnkColor = '#6c757d'; // Default color
                                    if (!empty($row['stnk_deadline'])) {
                                        try {
                                            $stnkDate = new DateTime($row['stnk_deadline']);
                                            $today = new DateTime();
                                            $isExpired = $stnkDate < $today;
                                            $interval = $today->diff($stnkDate);

                                            if ($isExpired) {
                                                $stnkText = "Kadaluarsa!";
                                                $stnkColor = '#ACB3B5'; // Abu-abu untuk kadaluarsa
                                            } elseif ($interval->y >= 1) {
                                                $stnkText = "{$interval->y} thn+";
                                                $stnkColor = 'black'; // Hitam untuk > 1 tahun
                                            } elseif ($interval->m >= 1) {
                                                $stnkText = "{$interval->m} bln";
                                                $stnkColor = '#CB7A01'; // Oranye untuk beberapa bulan
                                            } else {
                                                $stnkText = "{$interval->d} hr";
                                                $stnkColor = '#FF0000'; // Merah untuk beberapa hari
                                            }
                                        } catch (Exception $e) {
                                            // Biarkan default jika tanggal STNK tidak valid
                                        }
                                    }

                                    // Format harga
                                    $formattedPrice = "Rp " . number_format($row['price'], 0, ',', '.');

                                    // Menampilkan baris data (<tr>) ke dalam tabel HTML.
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['id']) . "</td>
                                            <td>" . htmlspecialchars($row['brand_model']) . "</td>
                                            <td>" . htmlspecialchars($typeVehicle) . "</td>
                                            <td style='color: {$stnkColor}; font-weight: bold;'>" . htmlspecialchars($stnkText) . "</td>
                                            <td>" . htmlspecialchars($formattedPrice) . "</td>
                                            <td><span class='badge' style='background-color: {$statusColor}; color: white;'>" . htmlspecialchars($statusText) . "</span></td>
                                            <td>{$row['branch_name']}</td>
                                            <td style='display: flex; align-items: center; gap: 8px;'>
                                                <a href='detail.php?id={$row['id']}' title='Detail' class='btn btn-secondary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white;'><i class='mdi mdi-eye'></i></a>
                                                <a href='edit.php?id={$row['id']}' title='Edit' class='btn btn-primary btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px;'><i class='mdi mdi-pencil'></i></a>
                                                <a href='softDelete.php?id={$row['id']}' title='Delete' class='btn btn-danger btn-sm d-flex justify-content-center align-items-center' style='width: 28px; height: 28px; border-radius: 4px; color: white;'><i class='mdi mdi-delete-restore' style='color: white;'></i></a>
                                            </td>
                                        </tr>";
                                }
                                // Jika tidak ada data kendaraan sama sekali, tampilkan pesan.
                                if (empty($dataVehicles)) {
                                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data kendaraan yang aktif.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php // Menampilkan navigasi pagination hanya jika total halaman lebih dari 1.
        if ($totalPages > 1) : ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' // Nonaktifkan jika di halaman pertama 
                                            ?>">
                        <a class="page-link bg-primary text-white" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <?php // Loop untuk menampilkan nomor-nomor halaman
                    for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' // Tandai sebagai aktif jika cocok dengan halaman saat ini 
                                                ?>">
                            <a class="page-link <?= ($page == $i) ? 'bg-primary text-white' : 'text-primary bg-white' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' // Nonaktifkan jika di halaman terakhir 
                                            ?>">
                        <a class="page-link bg-primary text-white" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <?php include '../layout/footer.php'; // Memuat bagian footer dari tata letak halaman.
        ?>
    </div>
</div>