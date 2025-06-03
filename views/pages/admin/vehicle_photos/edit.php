<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../helpers/functionResizeImage.php';
// 🔗 Hubungin ke function Resize Image

$id = $_GET['id'];
// 🪢 Ambil Id vehicle document dari URL

if (!$id) {
    // ❗ Kalau gak ada Id di URL
    die("Id tidak ditemukan.");
}

// 🪢 Ambil data vehicle document berdasarkan Slug
$data = $koneksi->prepare("SELECT * FROM vehicle_photos WHERE id = ? AND deleted_at IS NULL");
$data->execute([$id]);
$vehicle_photo = $data->fetch();

if (!$vehicle_photo) {
    // ❗ Kalau datanya gak ditemukan
    die("Data Vehicle Dokumen tidak ditemukan.");
}

// Handle update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $photo_path = '';

    if (isset($_FILES['photo_path']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
        // 🔥 Hapus file lama
        if (!empty($vehicle_photo['photo_path'])) {
            $oldPath = '../../../../storage/' . $vehicle_photo['photo_path'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $ext = pathinfo($_FILES['photo_path']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('photo_') . '.' . $ext;

        // 🗂️ Buat folder vehicle berdasarkan ID
        $vehicles_id = $vehicle_photo['vehicle_id'];
        $folderName = '../../../../storage/vehicle_photos/vehicle_' . $vehicles_id;

        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }

        $uploadPath = $folderName . '/' . $uniqueName;
        $tempPath = $_FILES['photo_path']['tmp_name'];

        // Resize dan simpan
        $resized = resizeImage($tempPath, $uploadPath);
        if ($resized) {
            $photo_path = 'vehicle_photos/vehicle_' . $vehicles_id . '/' . $uniqueName;
        } else {
            echo "❌ Gagal resize gambar.";
            exit;
        }
    }

    $data = $koneksi->prepare("UPDATE vehicle_photos SET photo_path = ?, updated_at = NOW() WHERE id = ?");
    $data->execute([$photo_path, $id]);

    header('Location: index.php');
    exit;
}

?>

<!-- 📅 Form edit data -->
<h2>Edit Photo Kendaraan</h2>
<form method="POST" enctype="multipart/form-data">
    Vehicle ID: 
    <input type="text" value="<?= $vehicle_photo['vehicle_id'] ?>" disabled><br>
    
    <img src="../../../../storage/<?= $vehicle_photo['photo_path'] ?>" width="100"><br>

    Photo: 
    <input type="file" name="photo_path" accept="image/*" required><br>

    <button type="submit">Update</button>
</form>