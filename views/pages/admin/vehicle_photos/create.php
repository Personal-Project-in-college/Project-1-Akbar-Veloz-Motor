<?php
include '../../../../config/koneksi.php';
// 🔗 Hubungkan ke file koneksi database
include '../../../../helpers/functionResizeImage.php';
// 🔗 Hubungin ke function Resize Image

// 🪢 Ambil kendaraan yang belum ada di vehicle_documents
$vehicles = $koneksi->query(" SELECT v.id FROM vehicles v LEFT JOIN (SELECT vehicle_id, COUNT(*) as photo_count FROM vehicle_photos GROUP BY vehicle_id) vp ON v.id = vp.vehicle_id WHERE (v.deleted_at IS NULL AND v.deleted_by_branch_at IS NULL) AND IFNULL(vp.photo_count, 0) < 5");



// 🔝 Kalau form dikirim (pakai POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🕹️ Proses form saat tombol submit diklik
    $vehicles_id = $_POST['vehicle_id'];

    // Validasi batas 5 foto dulu
    $photoCountStmt = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_photos WHERE vehicle_id = ?");
    $photoCountStmt->execute([$vehicles_id]);
    $photoCount = $photoCountStmt->fetchColumn();

    if ($photoCount >= 5) {
        echo "❌ Gagal: Maksimal 5 foto untuk kendaraan ini.";
        exit;
    }

    // Upload Photo Kendaraan
    $photo_path = '';
    if (isset($_FILES['photo_path']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo_path']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('photo_') . '.' . $ext;

        // 🗂️ Buat folder vehicle berdasarkan ID
        $folderName = '../../../../storage/vehicle_photos/vehicle_' . $vehicles_id;

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
            $photo_path = 'vehicle_photos/vehicle_' . $vehicles_id . '/' . $uniqueName;
        } else {
            echo "❌ Gagal resize gambar.";
            exit;
        }
    }


    // ⬇️ Simpan data ke database (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at)
    $data = $koneksi->prepare("INSERT INTO vehicle_photos (vehicle_id, photo_path, created_at) VALUES (?, ?, NOW())");
    $data->execute([$vehicles_id, $photo_path]);

    // 🚀 Setelah berhasil, balik ke halaman index
    header('Location: index.php');
    exit;
}
?>

<h2>Tambah Photo Kendaraan</h2>
<form method="POST" enctype="multipart/form-data">
    Vehicle ID:
    <select name="vehicle_id" required>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['id'] ?>"><?= $vehicle['id'] ?></option>
        <?php endforeach; ?>
    </select><br>

    Photo:
    <input type="file" name="photo_path" accept="image/*" required><br>

    <button type="submit">Tambah</button>
</form>