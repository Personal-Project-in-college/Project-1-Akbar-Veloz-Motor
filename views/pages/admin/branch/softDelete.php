<?php

/**
 * File: softDelete.php
 * Skrip ini bertanggung jawab untuk melakukan "soft delete" pada data cabang.
 * Ini tidak menghapus data secara permanen, melainkan mengisi kolom 'deleted_at'.
 * Proses ini juga melakukan soft delete bertingkat (cascade) ke data turunan:
 * 1. Kendaraan (vehicles)
 * 2. Dokumen Kendaraan (vehicle_documents)
 * 3. Foto Kendaraan (vehicle_photos)
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
    // PROSES SOFT DELETE BERTINGKAT (CASCADE)
    // ----------------------------------------

    // Langkah A: Ambil nama cabang SEBELUM dihapus untuk pesan notifikasi.
    $getNameQuery = $koneksi->prepare("SELECT name FROM branches WHERE id = ?");
    $getNameQuery->execute([$id]);
    $branchName = $getNameQuery->fetchColumn();

    // Langkah B: Soft delete data cabang induk.
    $softDeleteBranch = $koneksi->prepare("UPDATE branches SET deleted_at = NOW() WHERE id = ?");
    $softDeleteBranch->execute([$id]);

    // Langkah C: Soft delete semua kendaraan yang terkait dengan cabang ini.
    $softDeleteVehicles = $koneksi->prepare("UPDATE vehicles SET deleted_by_branch_at = NOW() WHERE branch_id = ?");
    $softDeleteVehicles->execute([$id]);

    // Langkah D: Soft delete dokumen kendaraan (lebih efisien tanpa foreach).
    // Query ini akan meng-update semua dokumen yang vehicle_id-nya ada di dalam cabang ini.
    $softDeleteDocuments = $koneksi->prepare("
        UPDATE vehicle_documents SET deleted_by_vehicle_at = NOW()
        WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ?)
    ");
    $softDeleteDocuments->execute([$id]);

    // Langkah E: Soft delete foto kendaraan (lebih efisien tanpa foreach).
    // Logika sama seperti dokumen, namun untuk tabel foto.
    $softDeletePhotos = $koneksi->prepare("
        UPDATE vehicle_photos SET deleted_by_vehicle_at = NOW()
        WHERE vehicle_id IN (SELECT id FROM vehicles WHERE branch_id = ?)
    ");
    $softDeletePhotos->execute([$id]);

    // Menyiapkan pesan notifikasi di session.
    if ($branchName) {
        $_SESSION['danger'] = "Cabang <strong>" . htmlspecialchars($branchName) . "</strong> berhasil dihapus sementara.";
    } else {
        $_SESSION['danger'] = "Data cabang berhasil dihapus sementara.";
    }
}

// 3. Arahkan pengguna kembali ke halaman utama data cabang.
header("Location: branch.php");
// 4. Hentikan eksekusi skrip setelah redirect.
exit;
