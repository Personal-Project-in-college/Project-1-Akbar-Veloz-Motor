<?php
require 'config/koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    die("Order ID tidak ditemukan.");
}

$stmt = $koneksi->prepare("SELECT created_at, type_order, vehicle_id, customer_id, status FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Data pesanan tidak ditemukan.");
}

$customer_id = $order['customer_id'] ?? null;

$alamat_customer = 'Alamat tidak ditemukan.';
$customer_latitude = null;
$customer_longitude = null;

if ($customer_id) {
    $stmtCustomer = $koneksi->prepare("SELECT address, latitude, longitude FROM customers WHERE id = ?");
    $stmtCustomer->execute([$customer_id]);
    $customer = $stmtCustomer->fetch(PDO::FETCH_ASSOC);

    if ($customer) {
        $alamat_customer = $customer['address'];
        $customer_latitude = $customer['latitude'];
        $customer_longitude = $customer['longitude'];
    }
}

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

    $updateVehicleQuery = $koneksi->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?");
    $updateVehicleQuery->execute([$vehicleId]);

    if ($order['type_order'] === 'test_driver') {
        $koneksi->prepare("UPDATE test_drivers SET status = 'cancelled' WHERE order_id = ?")
            ->execute([$order_id]);
    } elseif ($order['type_order'] === 'transaction') {
        $koneksi->prepare("UPDATE transactions SET status = 'cancelled' WHERE order_id = ?")
            ->execute([$order_id]);
    }

    header("Location: tunggu-petugas.php?id=$order_id");
    exit();
}

$Maps_embed_url = 'https://maps.google.com/maps?';
if ($customer_latitude && $customer_longitude) {
    $Maps_embed_url .= 'q=' . $customer_latitude . ',' . $customer_longitude;
} else {
    $Maps_embed_url .= 'q=' . urlencode($alamat_customer . ', Subang, Jawa Barat, Indonesia');
}
$Maps_embed_url .= '&t=&z=16&ie=UTF8&iwloc=&output=embed';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mohon Tunggu Petugas Kami</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/tunggu-petugas.css">
</head>

<body>
    <div class="status-card">
        <img src="./assets/icons/logo.png" alt="Delivery Icon" class="status-icon" />
        <h1>Mohon Tunggu Petugas Kami</h1>
        <p>Terima kasih. Petugas kami akan segera menghubungi Anda untuk konfirmasi dan akan menuju ke lokasi Anda sesuai jadwal yang ditentukan.</p>

        <div class="address-box">
            <strong>Tujuan Kunjungan:</strong><br>
            <?php echo htmlspecialchars($alamat_customer); ?>
        </div>

        <div class="map-container">
            <?php if ($customer_latitude && $customer_longitude) : ?>
                <iframe
                    src="<?= htmlspecialchars($Maps_embed_url); ?>"
                    class="map-frame" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            <?php else : ?>
                <p>Peta tidak dapat ditampilkan dengan koordinat karena tidak tersedia. Menampilkan peta berdasarkan alamat teks.</p>
                 <iframe
                    src="<?= htmlspecialchars($Maps_embed_url); ?>"
                    class="map-frame" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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