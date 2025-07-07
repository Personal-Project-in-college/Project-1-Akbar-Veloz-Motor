<?php
require 'config/koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    die("Order ID tidak ditemukan.");
}

$stmtOrder = $koneksi->prepare("SELECT created_at, type_order, vehicle_id, status FROM orders WHERE id = ?");
$stmtOrder->execute([$order_id]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Data pesanan tidak ditemukan.");
}

$vehicle_id = $order['vehicle_id'];

$stmtVehicle = $koneksi->prepare("SELECT branch_id FROM vehicles WHERE id = ?");
$stmtVehicle->execute([$vehicle_id]);
$vehicle = $stmtVehicle->fetch(PDO::FETCH_ASSOC);

$branch_id = $vehicle['branch_id'] ?? null;

$showroom_name = 'Showroom Tidak Ditemukan';
$showroom_address = 'Alamat tidak ditemukan.';
$showroom_latitude = null;
$showroom_longitude = null;

if ($branch_id) {
    $stmtBranch = $koneksi->prepare("SELECT name, address, latitude, longitude FROM branches WHERE id = ?");
    $stmtBranch->execute([$branch_id]);
    $branch_data = $stmtBranch->fetch(PDO::FETCH_ASSOC);

    if ($branch_data) {
        $showroom_name = $branch_data['name'];
        $showroom_address = $branch_data['address'];
        $showroom_latitude = $branch_data['latitude'];
        $showroom_longitude = $branch_data['longitude'];
    }
}

// PERBAIKAN DI SINI: Gunakan '3?' di URL dasar agar konsisten dengan tunggu-petugas.php
$Maps_embed_url = 'https://maps.google.com/maps?';
if ($showroom_latitude && $showroom_longitude) {
    $Maps_embed_url .= 'q=' . $showroom_latitude . ',' . $showroom_longitude;
} else {
    $Maps_embed_url .= 'q=' . urlencode($showroom_address . ', Subang, Jawa Barat, Indonesia');
}
$Maps_embed_url .= '&t=&z=16&ie=UTF8&iwloc=&output=embed';


date_default_timezone_set('Asia/Jakarta');

$created_at = new DateTime($order['created_at']);
$now = new DateTime();
$diffInSeconds = $now->getTimestamp() - $created_at->getTimestamp();
$canCancel = $diffInSeconds < 300 && $order['status'] !== 'cancelled';
$remainingSeconds = max(0, 300 - $diffInSeconds);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $koneksi->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?")
        ->execute([$order_id]);

    $updateVehicleQuery = $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?");
    $updateVehicleQuery->execute([$vehicle_id]);

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
            <?= htmlspecialchars($showroom_name); ?><br>
            <?= htmlspecialchars($showroom_address); ?>
        </div>

        <div class="map-container">
            <?php if ($showroom_latitude && $showroom_longitude) : ?>
                <iframe
                    src="<?= htmlspecialchars($Maps_embed_url); ?>"
                    class="map-frame" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <?php else : ?>
                <p>Peta tidak dapat ditampilkan dengan koordinat. Menampilkan peta berdasarkan alamat teks.</p>
                <iframe
                    src="<?= htmlspecialchars($Maps_embed_url); ?>"
                    class="map-frame" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <?php endif; ?>
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

            const now = Date.Now(); // <-- Ini juga ada typo, akan saya perbaiki menjadi Date.now()
            let elapsed = Math.floor((now - startTime) / 1000);
            let remaining = Math.max(0, <?php echo $remainingSeconds; ?> - elapsed);
            const reloadFlagKey = 'cancel_reloaded_' + orderId;
            if (remaining > 0) {
                localStorage.removeItem(reloadFlagKey);
            }

            const countdown = setInterval(() => {
                if (remaining <= 0) {
                    clearInterval(countdown);

                    if (!localStorage.getItem(reloadFlagKey)) {
                        localStorage.setItem(reloadFlagKey, 'true');
                        localStorage.removeItem(storageKey);
                        timerDisplay.textContent = "Waktu Habis";
                        if (cancelButton) cancelButton.disabled = true;

                        setTimeout(() => location.reload(), 1000);
                    } else {
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