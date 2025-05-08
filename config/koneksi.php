<?php

// Ambil isi file .env untuk konfigurasi database
$env = parse_ini_file(__DIR__ . '/../.env');

try {
    // Bikin koneksi ke database pakai PDO (lebih fleksibel & aman)
    $koneksi = new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']}",
        $env['DB_USER'],
        $env['DB_PASS']
    );

    // Aktifin mode error biar kalau ada masalah langsung dilempar
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Kalau koneksi gagal, tampilkan pesan error
    die("Koneksi gagal: " . $e->getMessage());
}
