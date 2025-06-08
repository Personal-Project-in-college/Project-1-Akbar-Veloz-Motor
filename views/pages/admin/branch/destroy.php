<?php

/**
 * File: destroy.php
 * Skrip ini bertanggung jawab untuk menghapus data cabang SECARA PERMANEN.
 * Ini adalah operasi "hard delete" yang juga akan menghapus semua data turunan
 * yang terkait dengan cabang tersebut (cascade delete), seperti:
 * 1. File foto fisik kendaraan.
 * 2. Record foto kendaraan dari database.
 * 3. Record dokumen kendaraan dari database.
 * 4. Record kendaraan itu sendiri.
 * 5. Record cabang.
 */

// ------------------------------
// INISIALISASI & KONFIGURASI
// ------------------------------

// PENTING: Memulai session agar bisa menggunakan $_SESSION untuk notifikasi.
session_start();

// 1. Mengimpor file koneksi database.
include '../../../../config/koneksi.php';

// 2. Memeriksa apakah pengguna sudah login.
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

// 3. Mengimpor fungsi untuk menghapus file foto fisik dari server.
include '../../../../helpers/functionDeleteFileVehiclePhoto.php';

include '../../../../helpers/functionDeleteFileVehicleDocument.php';

// 4. Memastikan ada 'id' yang dikirim melalui URL.
if (isset($_GET['id'])) {
    $branch_id = $_GET['id'];

    // ------------------------------
    // PROSES HAPUS BERTINGKAT (CASCADE DELETE)
    // ------------------------------

    // Langkah A: Ambil nama cabang SEBELUM dihapus, untuk pesan notifikasi.
    $getNameQuery = $koneksi->prepare("SELECT name FROM branches WHERE id = ?");
    $getNameQuery->execute([$branch_id]);
    $branchName = $getNameQuery->fetchColumn();

    // Langkah B: Ambil semua ID kendaraan yang berada di bawah cabang ini.
    $getVehicleIdsQuery = $koneksi->prepare("SELECT id FROM vehicles WHERE branch_id = ?");
    $getVehicleIdsQuery->execute([$branch_id]);
    $vehicleIds = $getVehicleIdsQuery->fetchAll(PDO::FETCH_COLUMN);

    // Langkah C: Hapus semua data turunan untuk setiap kendaraan.
    if (!empty($vehicleIds)) {
        foreach ($vehicleIds as $vehicleId) {
            // C.1. Hapus file-file foto fisik kendaraan dari server.
            deleteFileVehiclePhotos($koneksi, $vehicleId); // Memanggil helper.

            // C.2. Hapus record foto kendaraan dari database.
            $deletePhotosQuery = $koneksi->prepare("DELETE FROM vehicle_photos WHERE vehicle_id = ?");
            $deletePhotosQuery->execute([$vehicleId]);

            // C.3. Ambil semua ID dokumen untuk kendaraan ini
            $getDocumentIdsQuery = $koneksi->prepare("SELECT id FROM vehicle_documents WHERE vehicle_id = ?");
            $getDocumentIdsQuery->execute([$vehicleId]);
            $documentIds = $getDocumentIdsQuery->fetchAll(PDO::FETCH_COLUMN);

            // C.3.1 Hapus file dokumen + record dari DB via helper
            foreach ($documentIds as $documentId) {
                deleteFileVehicleDocument($koneksi, $documentId); // Memanggil helper
            }
        }

        // Langkah D: Setelah data turunan dari semua kendaraan dihapus, hapus record kendaraan itu sendiri.
        // Menghapus SEMUA kendaraan dalam cabang ini, mencegah data yatim piatu.
        $deleteVehiclesQuery = $koneksi->prepare("DELETE FROM vehicles WHERE branch_id = ?");
        $deleteVehiclesQuery->execute([$branch_id]);
    }

    // Langkah E: Hapus record cabang itu sendiri secara permanen.
    // Klausa "AND deleted_at IS NOT NULL" sebagai pengaman, memastikan hanya data dari "tong sampah" yang bisa dihapus permanen.
    $deleteBranchQuery = $koneksi->prepare("DELETE FROM branches WHERE id = ? AND deleted_at IS NOT NULL");
    $deleteBranchQuery->execute([$branch_id]);

    // Set notifikasi sukses setelah semua proses selesai.
    if ($branchName) {
        $_SESSION['danger'] = "Cabang <strong>" . htmlspecialchars($branchName) . "</strong> dan semua data terkait berhasil dihapus selamanya.";
    } else {
        $_SESSION['danger'] = "Data cabang dan semua data terkait berhasil dihapus selamanya.";
    }
}

// 5. Arahkan pengguna kembali ke halaman daftar data terhapus.
header('Location: delete.php');
// 6. Hentikan eksekusi skrip.
exit;
