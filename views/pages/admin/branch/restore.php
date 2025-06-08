<?php

/**
 * File: restore.php
 * Skrip ini bertanggung jawab untuk mengembalikan (restore) data cabang yang
 * telah dihapus sementara (soft-deleted). Proses ini berjalan secara bertingkat,
 * mengembalikan data turunan terlebih dahulu sebelum mengembalikan data induk.
 */

// ------------------------------
// INISIALISASI & KONFIGURASI
// ------------------------------

// PENTING: Memulai session agar bisa menggunakan $_SESSION untuk notifikasi.
session_start();

// 1. Mengimpor file-file yang diperlukan.
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin(); // Menjalankan pengecekan login

// 2. Memastikan ada 'id' yang dikirim melalui URL.
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ----------------------------------------
    // PROSES RESTORE BERTINGKAT (CASCADE RESTORE)
    // ----------------------------------------

    // Langkah A: Ambil nama cabang SEBELUM di-restore untuk pesan notifikasi.
    $getNameQuery = $koneksi->prepare("SELECT name FROM branches WHERE id = ?");
    $getNameQuery->execute([$id]);
    $branchName = $getNameQuery->fetchColumn();

    // Langkah B: Restore dokumen kendaraan yang terkait.
    // Query ini mencari semua vehicle_id dalam cabang ini yang statusnya terhapus oleh branch,
    // lalu mengembalikan dokumen-dokumen dari kendaraan tersebut yang juga terhapus.
    $restoreDocuments = $koneksi->prepare("
        UPDATE vehicle_documents 
        SET deleted_by_vehicle_at = NULL 
        WHERE vehicle_id IN (
            SELECT id FROM vehicles 
            WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL
        ) AND deleted_by_vehicle_at IS NOT NULL
    ");
    $restoreDocuments->execute([$id]);

    // Langkah C: Restore foto kendaraan yang terkait (logika sama seperti dokumen).
    $restorePhotos = $koneksi->prepare("
        UPDATE vehicle_photos 
        SET deleted_by_vehicle_at = NULL 
        WHERE vehicle_id IN (
            SELECT id FROM vehicles 
            WHERE branch_id = ? AND deleted_by_branch_at IS NOT NULL
        ) AND deleted_by_vehicle_at IS NOT NULL
    ");
    $restorePhotos->execute([$id]);

    // Langkah D: Restore data kendaraan dalam cabang tersebut.
    // Mengatur 'deleted_by_branch_at' menjadi NULL untuk semua kendaraan di cabang ini.
    $restoreVehicles = $koneksi->prepare("UPDATE vehicles SET deleted_by_branch_at = NULL WHERE branch_id = ?");
    $restoreVehicles->execute([$id]);

    // Langkah E: Restore data cabang itu sendiri.
    // Ini adalah langkah terakhir, mengatur 'deleted_at' menjadi NULL.
    $restoreBranch = $koneksi->prepare("UPDATE branches SET deleted_at = NULL WHERE id = ?");
    $restoreBranch->execute([$id]);

    // Menyiapkan pesan sukses di session.
    if ($branchName) {
        $_SESSION['success'] = "Cabang <strong>" . htmlspecialchars($branchName) . "</strong> berhasil dikembalikan.";
    } else {
        $_SESSION['success'] = "Data cabang berhasil dikembalikan.";
    }
}

// 3. Arahkan pengguna kembali ke halaman daftar data terhapus.
header('Location: delete.php');
// 4. Hentikan eksekusi skrip setelah redirect.
exit;
