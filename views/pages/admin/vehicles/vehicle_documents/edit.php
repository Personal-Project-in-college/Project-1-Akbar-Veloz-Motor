<?php
/**
 * File: edit.php
 * Deskripsi: Halaman untuk mengedit file-file dokumen (STNK, BPKB, dll.)
 * yang terhubung dengan sebuah kendaraan.
 */

session_start();

// 1. Impor file-file konfigurasi dan helper
include '../../../../../config/koneksi.php';
include '../../../../../helpers/functionResizeImage.php'; // Helper untuk resize gambar.
include '../../../../../helpers/functionCheckLogin.php';
checkLogin(); // Pastikan pengguna sudah login.


// 2. Ambil dan Validasi Data Dokumen Awal
// Ambil ID record dokumen dari URL.
$id = $_GET['id'];
if (!$id) die("Id tidak ditemukan.");

// Ambil data record dokumen dari database yang statusnya tidak terhapus.
$data = $koneksi->prepare("SELECT * FROM vehicle_documents WHERE id = ? AND deleted_at IS NULL");
$data->execute([$id]);
$vehicle_document = $data->fetch();
if (!$vehicle_document) die("Data Vehicle Dokumen tidak ditemukan atau sudah dihapus.");

// Simpan ID kendaraan dan tentukan folder utama untuk penyimpanan file.
$vehicle_id = $vehicle_document['vehicle_id'];
$baseFolder = '../../../../../storage/vehicles/vehicle_' . $vehicle_id . '/vehicle_documents';

/**
 * Fungsi helper untuk menangani proses upload file dokumen.
 *
 * @param string $inputName Nama dari elemen <input type="file"> di form HTML.
 * @param int    $vehicle_id ID kendaraan untuk path folder.
 * @param string $folderName Path folder fisik tempat menyimpan file.
 * @param string $oldFilePath Path file lama yang tersimpan di database untuk dihapus.
 * @return string Mengembalikan path file baru jika upload berhasil, atau path file lama jika tidak ada file baru diupload.
 */
function uploadDocument($inputName, $vehicle_id, $folderName, $oldFilePath)
{
    // Jika tidak ada file yang diupload untuk input ini, langsung kembalikan path file yang lama.
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) return $oldFilePath;

    // Jika ada file BARU, hapus dulu file LAMA untuk menghemat ruang.
    if ($oldFilePath) { // Pastikan oldFilePath tidak kosong
        $oldPhysicalPath = '../../../../../storage/' . $oldFilePath;
        if (file_exists($oldPhysicalPath)) {
            unlink($oldPhysicalPath);
        }
    }

    // Siapkan nama file baru yang unik untuk menghindari tabrakan nama.
    $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
    $uniqueName = $inputName . '_' . uniqid() . '.' . $ext;
    $uploadPath = $folderName . '/' . $uniqueName;
    $tmpPath = $_FILES[$inputName]['tmp_name'];

    // Periksa apakah file adalah gambar. Jika ya, resize. Jika tidak, langsung pindahkan.
    $imageExts = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array(strtolower($ext), $imageExts)) {
        // Panggil fungsi resize dari helper.
        $resized = resizeImage($tmpPath, $uploadPath);
        // Jika resize gagal, jangan lanjutkan dan kembalikan path lama.
        if (!$resized) return $oldFilePath;
    } else {
        // Untuk file selain gambar (misal: PDF), langsung pindahkan.
        move_uploaded_file($tmpPath, $uploadPath);
    }

    // Kembalikan path relatif file baru untuk disimpan ke database.
    return 'vehicles/vehicle_' . $vehicle_id . '/vehicle_documents/' . $uniqueName;
}


// 3. Proses Form Jika di-Submit (Metode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Panggil fungsi uploadDocument untuk setiap kemungkinan file yang diupload.
    // Jika tidak ada file baru, fungsi akan mengembalikan path file lama.
    $stnk       = uploadDocument('stnk', $vehicle_id, $baseFolder, $vehicle_document['stnk']);
    $bpkb       = uploadDocument('bpkb', $vehicle_id, $baseFolder, $vehicle_document['bpkb']);
    $service    = uploadDocument('service_note', $vehicle_id, $baseFolder, $vehicle_document['service_note']);
    $nota       = uploadDocument('nota', $vehicle_id, $baseFolder, $vehicle_document['nota']);
    $asuransi   = uploadDocument('asuransi', $vehicle_id, $baseFolder, $vehicle_document['asuransi']);

    // 4. Update Database dengan path file yang baru (atau yang lama jika tidak berubah).
    $data = $koneksi->prepare("UPDATE vehicle_documents SET stnk = ?, bpkb = ?, service_note = ?, nota = ?, asuransi = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$stnk, $bpkb, $service, $nota, $asuransi, $id]);

    // 5. Siapkan Pesan Sukses dan Redirect
    if ($vehicle_id) {
        $_SESSION['success'] = "Dokumen Kendaraan <strong>" . htmlspecialchars($vehicle_id) . "</strong> berhasil diupdate.";
    } else {
        $_SESSION['success'] = "Data Dokumen Kendaraan berhasil diupdate.";
    }

    // Arahkan kembali ke halaman detail kendaraan.
    header('Location: ../detail.php?id=' . urlencode($vehicle_id));
    exit;
}

// Catatan: Bagian di bawah ini akan berisi kode HTML untuk menampilkan form upload.
// Contoh: <form method="POST" enctype="multipart/form-data"> ... </form>
// Kode tersebut tidak disertakan dalam pertanyaan, jadi dokumentasi difokuskan pada logika PHP di atas.
?>