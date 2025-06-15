<?php
include '../../../../../config/koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photoId = $_POST['photo_id'] ?? null;
    $vehicleId = $_POST['vehicle_id'] ?? null;

    if (!$photoId || !$vehicleId) {
        $_SESSION['danger_message'] = "ID foto atau kendaraan tidak valid.";
        header("Location: ../detail.php?id=$vehicleId");
        exit;
    }

    try {
        // Cek apakah foto ini sudah cover
        $checkStmt = $koneksi->prepare("SELECT is_cover FROM vehicle_photos WHERE id = ? AND vehicle_id = ?");
        $checkStmt->execute([$photoId, $vehicleId]);
        $photo = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$photo) {
            $_SESSION['danger_message'] = "Foto tidak ditemukan.";
            header("Location: ../detail.php?id=$vehicleId");
            exit;
        }

        if ($photo['is_cover']) {
            // Lepaskan cover
            $unsetStmt = $koneksi->prepare("UPDATE vehicle_photos SET is_cover = 0, updated_at = NOW() WHERE id = ?");
            $unsetStmt->execute([$photoId]);
            $_SESSION['success_message'] = "Foto berhasil dilepas dari cover.";
        } else {
            // Set semua is_cover jadi 0 dulu
            $resetStmt = $koneksi->prepare("UPDATE vehicle_photos SET is_cover = 0 WHERE vehicle_id = ?");
            $resetStmt->execute([$vehicleId]);

            // Set foto ini jadi cover
            $setStmt = $koneksi->prepare("UPDATE vehicle_photos SET is_cover = 1, updated_at = NOW() WHERE id = ?");
            $setStmt->execute([$photoId]);
            $_SESSION['success_message'] = "Foto berhasil dijadikan cover.";
        }

        header("Location: ../detail.php?id=$vehicleId");
        exit;

    } catch (PDOException $e) {
        $_SESSION['danger_message'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: ../detail.php?id=$vehicleId");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
