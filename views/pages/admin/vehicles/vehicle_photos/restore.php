<?php
session_start();

// 1. Mengimpor file-file yang diperlukan.
include '../../../../../config/koneksi.php';
include '../../../../../helpers/functionCheckLogin.php';
checkLogin(); // Memastikan hanya pengguna yang sudah login yang bisa mengakses.


// 2. Mengambil ID dari URL
// ID kendaraan induk, diperlukan untuk notifikasi dan pengalihan (redirect).
$vehicle_id = $_GET['vehicle_id']; 

if (isset($_GET['id'])) {
    // ⬇️ Restore vehicle document
    $data = $koneksi->prepare("UPDATE vehicle_photos SET deleted_at = NULL WHERE id = ?");
    $data->execute([$_GET['id']]);
}

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