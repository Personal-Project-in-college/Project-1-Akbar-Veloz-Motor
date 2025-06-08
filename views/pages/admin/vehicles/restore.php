<?php

/**
 * File: restore.php
 * Deskripsi: Skrip untuk memulihkan ('restore') data kendaraan yang telah di-soft delete.
 * Proses ini mengosongkan (set ke NULL) kolom 'deleted_at' pada data kendaraan
 * dan juga pada data terkait seperti dokumen dan foto.
 */

// Memulai atau melanjutkan sesi yang ada untuk pesan notifikasi.
session_start();

// 1. Mengimpor file-file yang diperlukan.
include '../../../../config/koneksi.php';              // Koneksi ke database.
include '../../../../helpers/functionCheckLogin.php'; // Fungsi helper untuk otentikasi.
checkLogin();                                         // Memastikan pengguna sudah login.

// 2. Memeriksa keberadaan ID di URL
// Proses pemulihan hanya akan berjalan jika ada parameter 'id' yang dikirim melalui URL.
// Ini adalah langkah pengamanan untuk mencegah skrip berjalan tanpa target yang jelas.
if (isset($_GET['id'])) {

    $vehicleId = $_GET['id'];

    // 3. Mengambil ID untuk Pesan Notifikasi (Opsional)
    // Mirip seperti skrip hapus, ini mengambil ID dari DB untuk ditampilkan di pesan notifikasi.
    $getIdQuery = $koneksi->prepare("SELECT id FROM vehicles WHERE id = ?");
    $getIdQuery->execute([$vehicleId]);
    $vehicleIdForMessage = $getIdQuery->fetchColumn();

    // 4. Proses Pemulihan (Restore)
    // ⬆️ A. Restore data utama kendaraan.
    // Meng-update kolom 'deleted_at' menjadi NULL, yang menandakan data ini aktif kembali.
    $data = $koneksi->prepare("UPDATE vehicles SET deleted_at = NULL WHERE id = ?");
    $data->execute([$vehicleId]);

    // ⬆️ B. Cascading Restore: Pulihkan dokumen terkait.
    // Dokumen yang terkait dengan kendaraan ini juga diaktifkan kembali.
    $restoreDokumenKendaraan = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NULL WHERE vehicle_id = ?");
    $restoreDokumenKendaraan->execute([$vehicleId]);

    // ⬆️ C. Cascading Restore: Pulihkan foto terkait.
    // Foto-foto kendaraan ini juga ikut dipulihkan.
    $restorePhotoKendaraan = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NULL WHERE vehicle_id = ?");
    $restorePhotoKendaraan->execute([$vehicleId]);

    // 5. Menyiapkan Pesan Notifikasi Sukses
    // Menyiapkan pesan sukses yang akan ditampilkan di halaman berikutnya.
    if ($vehicleIdForMessage) {
        $_SESSION['success'] = "Kendaraan <strong>" . htmlspecialchars($vehicleIdForMessage) . "</strong> berhasil dikembalikan.";
    } else {
        $_SESSION['success'] = "Data kendaraan berhasil dikembalikan.";
    }
}

// 6. Mengalihkan Pengguna
// Setelah proses selesai, alihkan pengguna kembali ke halaman daftar data yang terhapus.
// Pengguna akan melihat data yang baru dipulihkan sudah hilang dari daftar ini.
header('Location: delete.php');
// Menghentikan eksekusi skrip setelah redirect.
exit;
