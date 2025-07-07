<?php
session_start();
include '../../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../../helpers/functionResizeImage.php';
// 🔗 Hubungin ke function Resize Image

$id = $_GET['id'];
if (!$id) die("Id tidak ditemukan.");

// Ambil data record dokumen dari database yang statusnya tidak terhapus.
$data = $koneksi->prepare("SELECT * FROM vehicle_photos WHERE id = ? AND deleted_at IS NULL");
$data->execute([$id]);
$vehicle_photo = $data->fetch();
if (!$vehicle_photo) die("Data Vehicle Foto tidak ditemukan atau sudah dihapus.");

// Simpan ID kendaraan dan tentukan folder utama untuk penyimpanan file.
$vehicle_id = $vehicle_photo['vehicle_id'];
$baseFolder = '../../../../../storage/vehicles/vehicle_' . $vehicle_id . '/vehicle_photos';

// Handle update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $photo_path = '';

    if (isset($_FILES['photo_path']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
        // 🔥 Hapus file lama
        if (!empty($vehicle_photo['photo_path'])) {
            $oldPath = '../../../../../storage/' . $vehicle_photo['photo_path'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $ext = pathinfo($_FILES['photo_path']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('photo_') . '.' . $ext;

        // 🗂️ Buat folder vehicle berdasarkan ID
        $vehicles_id = $vehicle_photo['vehicle_id'];
        $folderName = '../../../../../storage/vehicles/vehicle_' . $vehicles_id . '/vehicle_photos';

        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }

        $uploadPath = $folderName . '/' . $uniqueName;
        $tempPath = $_FILES['photo_path']['tmp_name'];

        // Resize dan simpan
        $resized = resizeImage($tempPath, $uploadPath);
        if ($resized) {
            $photo_path = 'vehicles/vehicle_' . $vehicles_id . '/vehicle_photos/' . $uniqueName;
        } else {
            echo "❌ Gagal resize gambar.";
            exit;
        }
    }

    $data = $koneksi->prepare("UPDATE vehicle_photos SET photo_path = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$photo_path, $id]);

    if ($vehicle_id) {
        $_SESSION['success_message'] = "Foto Kendaraan <strong>" . htmlspecialchars($vehicle_id) . "</strong> berhasil diupdate.";
    } else {
        $_SESSION['success_message'] = "Data Foto Kendaraan berhasil diupdate.";
    }

    header('Location: ../detail.php?id=' . urlencode($vehicle_id));
    exit;
}

?>