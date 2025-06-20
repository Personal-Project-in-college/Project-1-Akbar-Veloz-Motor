<?php
require 'config/koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    die("Order ID tidak ditemukan.");
}

// Ambil data created_at, type_order, dan status
$stmt = $koneksi->prepare("SELECT created_at, type_order, vehicle_id, status FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Data pesanan tidak ditemukan.");
}

// Tambahkan timezone
date_default_timezone_set('Asia/Jakarta');

$created_at = new DateTime($order['created_at']);
$now = new DateTime();
$diffInSeconds = $now->getTimestamp() - $created_at->getTimestamp();
$canCancel = $diffInSeconds < 300 && $order['status'] !== 'cancelled';
$remainingSeconds = max(0, 300 - $diffInSeconds);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $koneksi->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?")
        ->execute([$order_id]);

    $vehicleId = $order['vehicle_id'];

    // Update kendaraan jadi available
    $updateVehicleQuery = $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?");
    $updateVehicleQuery->execute([$vehicleId]);

    if ($order['type_order'] === 'test_driver') {
        $koneksi->prepare("UPDATE test_drivers SET status = 'cancelled' WHERE order_id = ?")
            ->execute([$order_id]);
    } elseif ($order['type_order'] === 'transaction') {
        $koneksi->prepare("UPDATE transactions SET status = 'cancelled' WHERE order_id = ?")
            ->execute([$order_id]);
    }

    header("Location: datang-ke-showroom.php?id=$order_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Janji Temu di Showroom</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/datang-ke-showroom.css">
</head>

<body>
    <div class="status-card">
        <img src="./assets/icons/logo.png" alt="Showroom Icon" class="status-icon" />
        <h1>Konfirmasi Janji Temu di Showroom</h1>
        <p>Terima kasih. Kami telah mencatat janji temu Anda. Silakan datang ke showroom kami sesuai jadwal yang telah ditentukan.</p>

        <div class="address-box">
            <strong>Alamat Showroom:</strong><br>
            Akbar Veloz Motor<br>
            Jl. Soekarno Hatta No. 123, Bandung, Jawa Barat
        </div>

        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31709.467082367086!2d107.76811926910234!3d-6.561590679615411!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e693b9ae39cc0eb%3A0x76db7b3959df2011!2sPoliteknik%20Negeri%20Subang%2C%20Kampus%20Utama%20Cibogo!5e0!3m2!1sid!2sid!4v1739072152156!5m2!1sid!2sid"
                class="map-frame" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" height="100%" width="100%"></iframe>
        </div>

        <div class="timer-info">
            <?php if ($order['status'] !== 'cancelled'): ?>
                <div id="cancel-timer">00:00</div>
            <?php endif; ?>

            <?php if ($canCancel): ?>
                <form method="POST">
                    <button type="submit" name="cancel" class="btn-cancel">Batalkan Pesanan</button>
                    <p class="mt-4">(Bisa dibatalkan dalam waktu 5 menit sejak dibuat)</p>
                </form>
            <?php elseif ($order['status'] === 'cancelled'): ?>
                <p>Pesanan telah dibatalkan sebelumnya.</p>
            <?php else: ?>
                <p>Batas waktu pembatalan telah lewat.</p>
            <?php endif; ?>
        </div>
        <div class="w-full d-flex justify-start">
            <a href="index.php" class="btn-secondary w-full">Kembali</a>
        </div>
    </div>
    <script>
        const remainingTime = <?php echo $remainingSeconds; ?>;
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timerDisplay = document.getElementById('cancel-timer');
            const cancelButton = document.querySelector('button[name="cancel"]');

            // Hentikan jika statusnya cancelled (timerDisplay tidak ada)
            if (!timerDisplay) return;

            const orderId = <?php echo json_encode($order_id); ?>;
            const storageKey = 'cancel_start_time_' + orderId;

            let startTime = localStorage.getItem(storageKey);
            if (!startTime) {
                startTime = Date.now();
                localStorage.setItem(storageKey, startTime);
            } else {
                startTime = parseInt(startTime);
            }

            const now = Date.now();
            let elapsed = Math.floor((now - startTime) / 1000);
            let remaining = Math.max(0, <?php echo $remainingSeconds; ?> - elapsed);
            const reloadFlagKey = 'cancel_reloaded_' + orderId;
            if (remaining > 0) {
                localStorage.removeItem(reloadFlagKey);
            }


            const countdown = setInterval(() => {
                if (remaining <= 0) {
                    clearInterval(countdown);

                    // Cek apakah sudah reload sebelumnya
                    if (!localStorage.getItem(reloadFlagKey)) {
                        localStorage.setItem(reloadFlagKey, 'true'); // tandai sudah reload
                        localStorage.removeItem(storageKey); // hapus waktu awal
                        timerDisplay.textContent = "Waktu Habis";
                        if (cancelButton) cancelButton.disabled = true;

                        setTimeout(() => location.reload(), 1000); // ⏱️ reload sekali
                    } else {
                        // Jika sudah reload, cukup tampilkan pesan
                        timerDisplay.textContent = "Waktu Habis";
                        if (cancelButton) cancelButton.disabled = true;
                    }
                } else {
                    const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
                    const seconds = String(remaining % 60).padStart(2, '0');
                    timerDisplay.textContent = `${minutes}:${seconds}`;
                    remaining--;
                }
            }, 1000);
        });
    </script>


</body>

</html>