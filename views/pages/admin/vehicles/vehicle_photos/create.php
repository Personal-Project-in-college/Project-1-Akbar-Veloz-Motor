<?php
session_start();
include '../../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../../helpers/functionResizeImage.php';
// 🔗 Hubungin ke function Resize Image

// 🪢 Ambil kendaraan yang belum ada di vehicle_documents
$vehicles = $koneksi->query(" SELECT v.id FROM vehicles v LEFT JOIN (SELECT vehicle_id, COUNT(*) as photo_count FROM vehicle_photos GROUP BY vehicle_id) vp ON v.id = vp.vehicle_id WHERE (v.deleted_at IS NULL AND v.deleted_by_branch_at IS NULL) AND IFNULL(vp.photo_count, 0) < 5");



// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $vehicles_id = $_POST['vehicle_id'];

    // Validasi batas 5 foto dulu
    $photoCountStmt = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_photos WHERE vehicle_id = ? AND (deleted_at IS NULL AND deleted_by_vehicle_at IS NULL)");
    $photoCountStmt->execute([$id]);
    $photoCount = $photoCountStmt->fetchColumn();

    if ($photoCount >= 6) {
        echo "❌ Gagal: Maksimal 6 foto untuk kendaraan ini.";
        exit;
    }

    // Upload Photo Kendaraan
    $photo_path = '';
    if (isset($_FILES['photo_path']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo_path']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('photo_') . '.' . $ext;

        // 🗂️ Buat folder vehicle berdasarkan ID
        $folderName = '../../../../../storage/vehicles/vehicle_' . $vehicles_id . '/vehicle_photos';

        // 🔨 Cek folder, kalau belum ada buat dulu
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true); // `true` biar bisa bikin folder parent kalau belum ada
        }

        // 📥 Pindahkan file ke folder yang sudah dibuat
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

   

    // ⬇️ Simpan data ke database (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at)
    $data = $koneksi->prepare("INSERT INTO vehicle_photos (vehicle_id, photo_path, created_at) VALUES (?, ?, NOW())");
    $data->execute([$vehicles_id, $photo_path]);

    if ($vehicles_id) {
        // Pesan yang lebih informatif jika ID kendaraan ada.
        $_SESSION['success'] = "Foto untuk kendaraan <strong>" . htmlspecialchars($vehicles_id) . "</strong> berhasil ditambahkan.";
    } else {
        // Pesan fallback jika `vehicle_id` tidak ada di URL.
        $_SESSION['success'] = "Data Foto Kendaraan berhasil ditambahkan.";
    }

    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: ../detail.php?id=' . urlencode($vehicles_id));
    exit;
}
