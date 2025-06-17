<?php
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Janji Temu di Showroom</title>
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
                class="map-frame"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade" height="100%" width="100%"></iframe>
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