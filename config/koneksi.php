<?php

// 📌 Ambil data dari file .env : Kode ini fungsinya buat ambil data konfigurasi database yang disimpan di file .env. Jadi isinya biasanya ada DB_HOST, DB_NAME, DB_USER, DB_PASS.
$env = parse_ini_file(__DIR__ . '/../.env');

try {
    // 📌 Bikin koneksi ke database pakai PDO : Di sini kita nyambungin aplikasi ke database MySQL pakai PDO (PHP Data Object). PDO ini fleksibel banget, bisa dipakai buat banyak jenis DB, dan lebih aman dari serangan SQL Injection kalau dipakai dengan prepare statement.
    $koneksi = new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']}",
        $env['DB_USER'],
        $env['DB_PASS']
    );

    // 📌 Aktifin mode error biar bisa nangkep errornya dengan baik : Kalau ada error saat query, nanti error-nya dilempar (throw) dalam bentuk exception, jadi gampang dilacak pas debug. Nggak diem-diem aja error-nya
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // 📌 Tangkap error kalau koneksi gagal : Kalau proses koneksi di atas gagal, masuk ke bagian ini. Dia bakal tampilin pesan error yang jelas, jadi tahu apa masalahnya (contoh: salah password DB, host nggak nyambung, dsb).
    die("Koneksi gagal: " . $e->getMessage());
}
