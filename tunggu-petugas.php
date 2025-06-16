<?php
$alamat_customer = "Jl. Merdeka No. 45, Bandung";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mohon Tunggu Petugas Kami</title>
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

        <div class="timer-info">
            <p>Anda dapat membatalkan janji temu ini dalam:</p>
            <div id="cancel-timer">05:00</div>
            <button id="cancel-button" class="btn-cancel">Batalkan Janji Temu</button>
        </div>
    </div>

    <script src="./js/timer.js"></script>

</body>

</html>