<?php
require_once __DIR__ . '/../vendor/autoload.php'; // pastikan path ini benar sesuai struktur project kamu

use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
\Midtrans\Config::$clientKey = $_ENV['MIDTRANS_CLIENT_KEY'];
\Midtrans\Config::$isProduction = false; // true kalau sudah live
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true; // untuk kartu kredit

// Optional: Cek koneksi berhasil
// echo "Midtrans config loaded";