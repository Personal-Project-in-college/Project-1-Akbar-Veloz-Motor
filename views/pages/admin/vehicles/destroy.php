<?php

/**
 * File: destroy.php (dalam folder vehicles)
 * Skrip ini bertanggung jawab untuk menghapus data kendaraan SECARA PERMANEN (hard delete).
 * Ini juga akan menghapus semua data turunan yang terkait langsung dengan kendaraan tersebut:
 * 1. Semua record dokumen kendaraan dari database.
 * 2. Semua file foto fisik kendaraan dari server (melalui helper).
 * 3. Semua record foto kendaraan dari database (melalui helper).
 * 4. Record kendaraan itu sendiri (jika sudah di-soft-delete).
 */

// 0. Inisialisasi dan Keamanan Dasar
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionDeleteFileVehiclePhoto.php'; // Tetap menggunakan helper ini

if (isset($_GET['id'])) {
    $id = $_GET['id']; // ID Kendaraan yang akan dihapus

    // 1. Ambil ID Kendaraan (atau info lain) untuk pesan notifikasi.
    // Ini juga sebagai validasi sederhana bahwa kendaraan ada.
    $getIdQuery = $koneksi->prepare("SELECT id FROM vehicles WHERE id = ?");
    $getIdQuery->execute([$id]);
    $vehicleIdentifier = $getIdQuery->fetchColumn();

    if ($vehicleIdentifier) { // Lanjutkan hanya jika kendaraan ditemukan

        // 2. Hapus SEMUA record dokumen kendaraan yang terkait dengan vehicle_id ini.
        $deleteDocumentsQuery = $koneksi->prepare("DELETE FROM vehicle_documents WHERE vehicle_id = ?");
        $deleteDocumentsQuery->execute([$id]);

        // 3. Persiapan untuk menghapus foto kendaraan menggunakan helper:
        //    A. Soft-delete terlebih dahulu SEMUA record foto yang terkait dengan vehicle_id ini
        //       yang mungkin belum di-soft-delete. Ini agar helper bisa memprosesnya.
        //       Kita asumsikan kolom 'deleted_at' adalah penanda soft-delete utama di vehicle_photos.
        $softDeleteAllPhotosQuery = $koneksi->prepare("UPDATE vehicle_photos SET deleted_at = NOW() WHERE vehicle_id = ? AND deleted_at IS NULL");
        $softDeleteAllPhotosQuery->execute([$id]);

        //    B. Panggil helper untuk menghapus file fisik dan record foto yang sudah di-soft-delete.
        //       Karena langkah A, semua foto dari kendaraan ini sekarang sudah ditandai,
        //       sehingga helper akan membersihkan semuanya.
        deleteFileVehiclePhotos($koneksi, $id);

        // 4. Hapus record kendaraan utama dari tabel 'vehicles'.
        // Klausa WHERE memastikan hanya kendaraan yang sudah di-soft delete
        // yang bisa dihapus permanen. Ini adalah lapisan pengaman.
        $deleteVehicleQuery = $koneksi->prepare(
            "DELETE FROM vehicles WHERE id = ? AND (deleted_at IS NOT NULL OR deleted_by_branch_at IS NOT NULL)"
        );
        $deleteVehicleQuery->execute([$id]);

        $_SESSION['danger'] = "Kendaraan ID <strong>" . htmlspecialchars($vehicleIdentifier) . "</strong> dan semua data terkait berhasil dihapus selamanya.";
    } else {
        $_SESSION['danger'] = "Kendaraan tidak ditemukan atau sudah dihapus sebelumnya.";
    }
} else {
    $_SESSION['danger'] = "Parameter ID kendaraan tidak valid.";
}

// 5. Arahkan pengguna kembali ke halaman daftar data terhapus.
header('Location: delete.php');
exit;
