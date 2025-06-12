<?php
session_start();
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $getPartnerQuery = $koneksi->prepare("SELECT name FROM partners WHERE id = ? AND deleted_at IS NOT NULL");
    $getPartnerQuery->execute([$id]);
    $partnerName = $getPartnerQuery->fetchColumn();

    if (!$partnerName) {
        echo json_encode(['success' => false, 'message' => "Data tidak ditemukan atau sudah dihapus permanent."]);
        exit;
    }

    $checkActiveLoanPartnerQuery = $koneksi->prepare("SELECT COUNT(*) FROM vehicle_loans WHERE partner_id = ? AND status = 'borrowed'");
    $checkActiveLoanPartnerQuery->execute([$id]);
    $countActiveLoans = $checkActiveLoanPartnerQuery->fetchColumn();

    if ($countActiveLoans > 0) {
        echo json_encode(['success' => false, 'message' => "Partner <strong>" . htmlspecialchars($partnerName) . "</strong> masih memiliki peminjaman aktif dan tidak dapat dihapus."]);
        exit;
    }

    $getSlugPartnerQuery = $koneksi->prepare("SELECT slug, name FROM partners WHERE id = ? AND deleted_at IS NOT NULL");
    $getSlugPartnerQuery->execute([$id]);
    $partnerSlug = $getSlugPartnerQuery->fetch(PDO::FETCH_ASSOC);

    if ($partnerSlug) {
        $slug = $partnerSlug['slug'];

        // Path folder dokumen partner
        $partnerFolder = '../../../../storage/partners/partners_' . $slug;

        // Hapus folder dan isinya jika ada
        if (is_dir($partnerFolder)) {
            function deleteFolder($folderPath)
            {
                foreach (scandir($folderPath) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    $path = $folderPath . DIRECTORY_SEPARATOR . $item;
                    is_dir($path) ? deleteFolder($path) : unlink($path);
                }
                rmdir($folderPath);
            }
            deleteFolder($partnerFolder);
        }

        $destroyVehiclesLoansQuery = $koneksi->prepare("DELETE FROM vehicle_loans WHERE partner_id = ? AND deleted_by_partner_at IS NOT NULL");
        $destroyVehiclesLoansQuery->execute([$id]);

        $destroyPartnerQuery = $koneksi->prepare("DELETE FROM partners WHERE id = ? AND deleted_at IS NOT NULL");
        $isDestroy = $destroyPartnerQuery->execute([$id]);

        if ($isDestroy) {
            echo json_encode(['success' => true, 'message' => "Partner <strong>" . htmlspecialchars($partnerName) . "</strong> berhasil dihapus permanent."]);
        } else {
            echo json_encode(['success' => false, 'message' => "Terjadi kesalahan saat hapus permanent partner."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
    }
}
