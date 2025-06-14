<?php

// 📌 Ambil data dari file .env
$env = parse_ini_file(__DIR__ . '/../.env');

// Jika parse_ini_file gagal karena sintaks error di .env, $env akan menjadi false.
if ($env === false) {
    die("Error: Gagal membaca file .env. Periksa sintaks di dalam file tersebut.");
}

try {
    // 📌 Bikin koneksi ke database pakai PDO
    // PERBAIKAN: Menambahkan ";port={$env['DB_PORT']}" ke dalam string koneksi.
    // Ini sangat penting untuk MAMP yang sering menggunakan port non-default seperti 8889.
    $koneksi = new PDO(
        "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_NAME']}",
        $env['DB_USER'],
        $env['DB_PASS']
    );

    // 📌 Aktifin mode error PDO
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // 📌 Tangkap error kalau koneksi gagal
    die("Koneksi gagal: " . $e->getMessage());
}