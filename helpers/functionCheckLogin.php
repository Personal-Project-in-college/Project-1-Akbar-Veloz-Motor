<?php

/**
 * File: functionCheckLogin.php
 * Berisi fungsi helper yang dapat digunakan kembali untuk memeriksa otentikasi pengguna.
 */

/**
 * Memeriksa apakah pengguna sudah login berdasarkan session.
 * * Fungsi ini melakukan dua hal utama:
 * 1. Memastikan sesi PHP sudah aktif. Jika belum, fungsi ini akan memulainya.
 * Ini mencegah error jika session_start() dipanggil lebih dari sekali.
 * 2. Memeriksa apakah variabel session 'user_id' sudah di-set.
 * * Jika 'user_id' tidak ada, fungsi ini akan mengalihkan pengguna
 * ke halaman login dan menghentikan eksekusi skrip selanjutnya.
 * * @return void Fungsi ini tidak mengembalikan nilai.
 */
function checkLogin()
{
    // Langkah 1: Periksa apakah sesi sudah berjalan.
    // Jika belum (statusnya PHP_SESSION_NONE), maka mulai sesi.
    // Ini adalah cara aman untuk menjalankan session_start().
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Langkah 2: Periksa apakah kunci 'user_id' ada di dalam array $_SESSION.
    // Jika tidak ada, berarti pengguna belum login.
    if (!isset($_SESSION['user_id'])) {
        // Alihkan pengguna ke halaman login.
        // CATATAN: Path relatif seperti '../auth/login.php' bergantung pada struktur folder
        // tempat fungsi ini dipanggil. Untuk aplikasi yang lebih besar, disarankan
        // menggunakan path absolut atau konstanta BASE_URL.
        header('Location: ../auth/login.php');

        // Hentikan eksekusi skrip untuk mencegah kode di bawahnya berjalan.
        exit;
    }
}
