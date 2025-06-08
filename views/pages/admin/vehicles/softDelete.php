<?php

/**
 * File: softDelete.php
 * Deskripsi: Skrip untuk melakukan 'soft delete' (hapus sementara) pada data kendaraan.
 * Proses ini tidak menghapus data secara permanen, melainkan mengisi kolom 'deleted_at'
 * dengan tanggal saat ini. Ini juga berlaku untuk data terkait seperti dokumen dan foto.
 */

// Memulai atau melanjutkan sesi yang ada, diperlukan untuk menyimpan pesan notifikasi.
session_start();

// 1. Mengimpor file-file yang diperlukan.
include '../../../../config/koneksi.php';              // Menghubungkan ke konfigurasi database.
include '../../../../helpers/functionCheckLogin.php'; // Memuat fungsi untuk memeriksa status login.
checkLogin();                                         // Menjalankan fungsi untuk memastikan pengguna sudah login.

// 2. Mengambil ID Kendaraan dari URL
// 🪢 Ambil ID unik kendaraan dari parameter URL (?id=...).
// Ini adalah target data yang akan kita hapus sementara.
$id = $_GET['id'];

// 3. Mengambil ID untuk Pesan Notifikasi (Opsional)
// Kode ini mengambil ID dari database sebelum dihapus. Tujuannya hanya untuk menampilkan ID tersebut di pesan notifikasi.
// Catatan: Langkah ini bisa dilewati jika pesan notifikasi tidak memerlukan info spesifik dari database,
// karena kita sebenarnya sudah punya nilai ID di variabel $id.
$getIdQuery = $koneksi->prepare("SELECT id FROM vehicles WHERE id = ?");
$getIdQuery->execute([$id]);
$vehicleId = $getIdQuery->fetchColumn();

// 4. Proses Soft Delete
// ⬇️ A. Soft delete data utama kendaraan.
// Meng-update kolom 'deleted_at' dengan timestamp saat ini (NOW()).
// Kendaraan ini tidak akan muncul lagi di halaman data aktif.
$data = $koneksi->prepare("UPDATE vehicles SET deleted_at = NOW() WHERE id = ?");
$data->execute([$id]);

// ⬇️ B. Cascading Soft Delete: Hapus sementara dokumen terkait.
// Ini penting agar dokumen dari kendaraan yang dihapus juga ikut "disembunyikan".
$hapusDokumenKendaraan = $koneksi->prepare("UPDATE vehicle_documents SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
$hapusDokumenKendaraan->execute([$id]);

// ⬇️ C. Cascading Soft Delete: Hapus sementara foto terkait.
// Sama seperti dokumen, foto-foto kendaraan ini juga ikut ditandai terhapus.
$hapusPhotoKendaraan = $koneksi->prepare("UPDATE vehicle_photos SET deleted_by_vehicle_at = NOW() WHERE vehicle_id = ?");
$hapusPhotoKendaraan->execute([$id]);


// 5. Menyiapkan Pesan Notifikasi
// Menyiapkan pesan yang akan ditampilkan di halaman berikutnya menggunakan session.
// Catatan: Key 'danger' biasanya untuk pesan error (merah). Untuk sukses, 'success' (hijau) mungkin lebih cocok.
if ($vehicleId) {
    // Pesan jika ID berhasil diambil, lebih spesifik.
    $_SESSION['danger'] = "Kendaraan <strong>" . htmlspecialchars($vehicleId) . "</strong> berhasil dihapus sementara.";
} else {
    // Pesan fallback jika karena suatu hal ID tidak terambil.
    $_SESSION['danger'] = "Data Kendaraan berhasil dihapus sementara.";
}

// 6. Mengalihkan Pengguna
// 🚀 Setelah semua proses selesai, alihkan pengguna kembali ke halaman daftar kendaraan.
header("Location: vehicles.php");
// Menghentikan eksekusi skrip setelah redirect untuk memastikan tidak ada kode lain yang berjalan.
exit;
