<?php
/**
 * File: restore.php
 * Deskripsi: Skrip untuk memulihkan ('restore') satu data dokumen kendaraan
 * yang sebelumnya telah di-soft delete.
 */

// Memulai sesi untuk bisa menggunakan variabel $_SESSION untuk notifikasi.
session_start();

// 1. Mengimpor file-file yang diperlukan.
include '../../../../../config/koneksi.php';
include '../../../../../helpers/functionCheckLogin.php';
checkLogin(); // Memastikan hanya pengguna yang sudah login yang bisa mengakses.


// 2. Mengambil ID dari URL
// ID kendaraan induk, diperlukan untuk notifikasi dan pengalihan (redirect).
$vehicle_id = $_GET['vehicle_id']; 

// 3. Proses Pemulihan (Restore)
// Memeriksa apakah 'id' dokumen ada di URL. Ini untuk memastikan kita punya target yang akan di-restore.
if (isset($_GET['id'])) {
    $document_id = $_GET['id'];
    
    // ⬇️ Menjalankan query untuk memulihkan dokumen.
    // Caranya dengan mengubah kolom 'deleted_at' menjadi NULL.
    // Ini menandakan bahwa data tersebut aktif kembali.
    $data = $koneksi->prepare("UPDATE vehicle_documents SET deleted_at = NULL WHERE id = ?");
    $data->execute([$document_id]);
}

// 4. Menyiapkan Pesan Notifikasi Sukses
// Pesan ini akan ditampilkan di halaman detail setelah pengguna dialihkan.
if ($vehicle_id) {
    // Pesan jika ID kendaraan berhasil didapat.
    $_SESSION['success'] = "Dokumen Kendaraan <strong>" . htmlspecialchars($vehicle_id) . "</strong> berhasil dikembalikan.";
} else {
    // Pesan fallback jika ID kendaraan tidak ada.
    $_SESSION['success'] = "Data Dokumen Kendaraan berhasil dikembalikan.";
}

// 5. Mengalihkan Pengguna
// Mengembalikan pengguna ke halaman detail kendaraan yang sesuai.
// Pengguna akan langsung melihat dokumen yang baru dipulihkan muncul kembali di daftar.
header('Location: ../detail.php?id=' . urlencode($vehicle_id));
exit; // Menghentikan eksekusi skrip setelah redirect.