<?php

/**
 * File: create_document.php (Asumsi nama file jika ini untuk halaman 'create' dokumen)
 * Deskripsi: Halaman untuk menambahkan set dokumen baru (STNK, BPKB, dll.) untuk sebuah kendaraan.
 * Menampilkan form dengan pilihan kendaraan yang belum memiliki dokumen, dan memproses upload file.
 */

session_start();

// 1. Impor file-file konfigurasi dan helper
include '../../../../../config/koneksi.php';
include '../../../../../helpers/functionResizeImage.php'; // Helper untuk resize gambar.
// Pengecekan login tidak ada di sini, asumsikan sudah dihandle di level yang lebih tinggi atau akan ditambahkan.
// Jika diperlukan, tambahkan:
// include '../../../../../helpers/functionCheckLogin.php';
// checkLogin();


// 2. Ambil Data Kendaraan yang Belum Punya Dokumen
// Query ini mengambil ID kendaraan yang:
// - Tidak ada di tabel `vehicle_documents` (artinya belum punya record dokumen sama sekali).
// - DAN aktif (deleted_at IS NULL dan deleted_by_branch_at IS NULL).
// Hasilnya akan digunakan untuk mengisi dropdown pilihan kendaraan di form.
$vehicles = $koneksi->query("SELECT id, brand_model FROM vehicles WHERE id NOT IN (SELECT vehicle_id FROM vehicle_documents) AND (deleted_at IS NULL AND deleted_by_branch_at IS NULL) ORDER BY brand_model ASC"); // Jangan lupa fetchAll() jika akan diloop di HTML, atau biarkan sebagai PDOStatement jika hanya perlu dicek.


// 3. Proses Form Jika di-Submit (Metode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id'];

    
    // 3.1 Validasi Logika Bisnis: Apakah kendaraan ini boleh ditambah dokumen?
    // Cek apakah sudah ada dokumen AKTIF untuk kendaraan ini.
    $cekVehicleDocs = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = ? AND deleted_at IS NULL AND deleted_by_vehicle_at IS NULL");
    $cekVehicleDocs->execute([$vehicle_id]); // Perbaikan: Gunakan $vehicle_id dari POST
    $adaDokumenAktif = $cekVehicleDocs->fetchColumn();

    // Cek apakah ada dokumen yang pernah di SOFT-DELETE untuk kendaraan ini.
    $cekDeletedVehicleDocs = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_documents WHERE vehicle_id = ? AND (deleted_at IS NOT NULL OR deleted_by_vehicle_at IS NOT NULL)");
    $cekDeletedVehicleDocs->execute([$vehicle_id]); // Perbaikan: Gunakan $vehicle_id dari POST
    $adaDokumenSoftDelete = $cekDeletedVehicleDocs->fetchColumn();

    // Aturan: Boleh tambah dokumen JIKA:
    // 1. TIDAK ADA dokumen aktif sama sekali (baru pertama kali input).
    // ATAU
    // 2. ADA dokumen yang pernah di-soft-delete (artinya, set dokumen lama sudah tidak aktif).
    //    Ini memungkinkan untuk "mengganti" set dokumen lama dengan yang baru.
    $bolehTambahDokumen = ($adaDokumenAktif == 0 && $adaDokumenSoftDelete >= 0);

    if (!$bolehTambahDokumen) {
        // Jika tidak memenuhi syarat, hentikan proses dan beri pesan.
        // Sebaiknya ini dihandle dengan $_SESSION['error'] dan redirect agar UI lebih baik.
        $_SESSION['error'] = "Gagal: Kendaraan ini sudah memiliki set dokumen aktif. Anda tidak bisa menambahkan lagi kecuali set dokumen lama dihapus (soft delete).";
        header('Location: ../detail.php?id=' . urlencode($vehicle_id)); // Asumsi nama file ini create.php
        exit;
    }

    // 3.2 Buat Folder Penyimpanan Jika Belum Ada
    // Folder dinamis berdasarkan ID kendaraan.
    $baseFolder = '../../../../../storage/vehicles/vehicle_' . $vehicle_id . '/vehicle_documents';
    if (!is_dir($baseFolder)) {
        mkdir($baseFolder, 0777, true); // 0777 adalah izin folder, true untuk membuat parent directory jika belum ada.
    }

    /**
     * Fungsi helper lokal untuk menangani proses upload satu file dokumen.
     *
     * @param string $inputName Nama dari elemen <input type="file"> di form.
     * @param int    $vehicle_id ID kendaraan untuk path folder.
     * @param string $folderName Path folder fisik tempat menyimpan file.
     * @return string Mengembalikan path file relatif jika upload berhasil, atau string kosong jika gagal/tidak ada file.
     */
    function uploadDocument($inputName, $vehicle_id, $folderName)
    {
        // Jika tidak ada file yang diupload atau ada error saat upload, kembalikan string kosong.
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) return '';

        $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $uniqueName = $inputName . '_' . uniqid() . '.' . $ext; // Buat nama file unik.
        $uploadPath = $folderName . '/' . $uniqueName; // Path lengkap untuk menyimpan file di server.
        $tmpPath = $_FILES[$inputName]['tmp_name']; // Path sementara file yang diupload.

        // Jika file adalah gambar, coba resize. Jika bukan, langsung pindahkan.
        $imageExts = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array(strtolower($ext), $imageExts)) {
            $resized = resizeImage($tmpPath, $uploadPath); // Panggil fungsi dari helper.
            if (!$resized) return ''; // Jika resize gagal, kembalikan string kosong.
        } else {
            // Untuk file non-gambar (PDF, DOCX, dll.), langsung pindahkan.
            if (!move_uploaded_file($tmpPath, $uploadPath)) {
                return ''; // Jika gagal memindahkan, kembalikan string kosong
            }
        }
        // Kembalikan path relatif file yang berhasil diupload untuk disimpan ke database.
        return 'vehicles/vehicle_' . $vehicle_id . '/vehicle_documents/' . $uniqueName;
    }

    // 3.3 Proses Upload Setiap File Dokumen
    $stnk       = uploadDocument('stnk', $vehicle_id, $baseFolder);
    $bpkb       = uploadDocument('bpkb', $vehicle_id, $baseFolder);
    $service    = uploadDocument('service_note', $vehicle_id, $baseFolder);
    $nota       = uploadDocument('nota', $vehicle_id, $baseFolder);
    $asuransi   = uploadDocument('asuransi', $vehicle_id, $baseFolder);

    // 3.4 Simpan Path File ke Database
    // Membuat record baru di tabel `vehicle_documents`.
    $stmt = $koneksi->prepare(
        "INSERT INTO vehicle_documents 
        (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt->execute([$vehicle_id, $stnk, $bpkb, $service, $nota, $asuransi]);

    // 3.5 Siapkan Pesan Sukses dan Redirect
    if ($vehicle_id) { // Cek $vehicle_id sekali lagi untuk konsistensi pesan
        $_SESSION['success'] = "Dokumen Kendaraan <strong>" . htmlspecialchars($vehicle_id) . "</strong> berhasil ditambahkan.";
    } else {
        // Ini seharusnya tidak terjadi jika $vehicle_id dari POST selalu ada.
        $_SESSION['success'] = "Data Dokumen Kendaraan berhasil ditambahkan.";
    }

    // Arahkan ke halaman detail kendaraan yang baru ditambahkan dokumennya.
    header('Location: ../detail.php?id=' . urlencode($vehicle_id));
    exit;
}
