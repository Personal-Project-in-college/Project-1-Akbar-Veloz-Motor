<?php

/**
 * File: functionShowAlert.php
 * Berisi fungsi helper untuk menampilkan notifikasi flash (flash message)
 * yang diambil dari variabel session.
 */

/**
 * Menampilkan notifikasi Bootstrap berdasarkan session.
 *
 * Fungsi ini memeriksa keberadaan `$_SESSION['success']` atau `$_SESSION['danger']`.
 * Jika ada, fungsi ini akan mencetak (echo) komponen alert Bootstrap yang sesuai.
 * Notifikasi ini akan hilang secara otomatis setelah 2 detik menggunakan JavaScript.
 * Setelah ditampilkan, variabel session tersebut akan dihapus (unset) agar tidak muncul lagi.
 *
 * @return void Fungsi ini tidak mengembalikan nilai, hanya mencetak output HTML & JS.
 */
function showAlert()
{
    // Pastikan sesi sudah berjalan agar $_SESSION dapat diakses.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Tentukan tipe alert dan pesan berdasarkan session yang tersedia.
    $alertType = '';
    $message = '';

    if (isset($_SESSION['success'])) {
        $alertType = 'success';
        $message = $_SESSION['success'];
    } elseif (isset($_SESSION['danger'])) {
        $alertType = 'danger';
        $message = $_SESSION['danger'];
    }
    // CATATAN: Kamu bisa dengan mudah menambahkan jenis alert lain di sini.
    // else if (isset($_SESSION['warning'])) {
    //     $alertType = 'warning';
    //     $message = $_SESSION['warning'];
    // }

    // Jika ada pesan yang harus ditampilkan (baik success maupun danger), cetak alert-nya.
    if ($message && $alertType) {
        // ID unik untuk JavaScript, dibuat berdasarkan waktu agar tidak konflik.
        $alertId = 'custom-alert-' . time();

        // Cetak blok HTML dan JavaScript hanya sekali.
        echo "
        <div class='alert alert-{$alertType} alert-dismissible fade show' role='alert' id='{$alertId}'>
            {$message}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        <script>
            setTimeout(() => {
                const alert = document.getElementById('{$alertId}');
                if (alert) {
                    // Menggunakan Bootstrap 5 JS untuk menghilangkan alert dengan efek fade
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 2000); // Alert akan hilang setelah 2 detik
        </script>";

        // Hapus session setelah ditampilkan agar tidak muncul lagi.
        unset($_SESSION[$alertType]);
    }
}
