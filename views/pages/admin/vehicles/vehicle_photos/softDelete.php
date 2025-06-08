<?php
/**
 * File: softDelete.php
 * Deskripsi: Skrip untuk melakukan 'soft delete' pada SATU file foto kendaraan.
 * Proses ini menargetkan satu baris di tabel `vehicle_documents` berdasarkan ID foto.
 */

// Memulai atau melanjutkan sesi yang ada untuk menyimpan pesan notifikasi.
session_start();

// 1. Mengimpor file-file yang diperlukan.
include '../../../../../config/koneksi.php';              // Koneksi ke database.
include '../../../../../helpers/functionCheckLogin.php'; // Fungsi helper untuk otentikasi.
checkLogin();                                         // Memastikan pengguna sudah login.


// 2. Mengambil ID dari URL
// Mengambil ID foto yang akan dihapus. Ini adalah Primary Key dari tabel `vehicle_documents`.
$id = $_GET['id'];
// Mengambil ID kendaraan induk. Ini dibutuhkan untuk pesan notifikasi dan untuk mengarahkan kembali ke halaman detail yang benar.
$vehicle_id = $_GET['vehicle_id'];


// 3. Proses Soft Delete foto
// Menjalankan query UPDATE untuk menandai satu foto sebagai terhapus.
// Kolom `deleted_at` diisi dengan timestamp saat ini, sehingga data tidak akan tampil lagi tapi masih ada di database.
$data = $koneksi->prepare("UPDATE vehicle_photos SET deleted_at = NOW() WHERE id = ?");
$data->execute([$id]);


// 4. Menyiapkan Pesan Notifikasi
// Pesan akan muncul di halaman detail setelah redirect.
// Catatan: Key 'danger' biasanya untuk error, 'success' mungkin lebih cocok untuk aksi yang berhasil.
if ($vehicle_id) {
    // Pesan yang lebih informatif jika ID kendaraan ada.
    $_SESSION['danger'] = "Foto untuk kendaraan <strong>" . htmlspecialchars($vehicle_id) . "</strong> berhasil dihapus sementara.";
} else {
    // Pesan fallback jika `vehicle_id` tidak ada di URL.
    $_SESSION['danger'] = "Data Foto Kendaraan berhasil dihapus sementara.";
}


// 5. Mengalihkan Pengguna Kembali
// Mengarahkan pengguna kembali ke halaman detail kendaraan tempat foto tadi berada.
// `urlencode()` digunakan sebagai praktik baik untuk memastikan ID aman untuk digunakan di dalam URL.
header('Location: ../detail.php?id=' . urlencode($vehicle_id));
// Menghentikan eksekusi skrip setelah redirect.
exit;