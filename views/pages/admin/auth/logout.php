<?php
/**
 * File: logout.php
 * Skrip ini bertanggung jawab untuk mengakhiri sesi (session) pengguna yang sedang aktif,
 * secara efektif mengeluarkan mereka (logout) dari sistem.
 */

// Langkah 1: Memulai atau melanjutkan sesi yang ada.
// Ini adalah langkah WAJIB sebelum dapat memanipulasi atau menghancurkan sesi.
session_start();

// Langkah 2: Menghapus semua variabel yang terdaftar di dalam sesi.
// Contoh: $_SESSION['user_id'], $_SESSION['name'], dll. akan dihapus.
// Ini membersihkan data dari array $_SESSION.
session_unset();

// Langkah 3: Menghancurkan semua data yang terkait dengan sesi saat ini di sisi server.
// Ini adalah langkah final dan terpenting untuk proses logout yang aman.
session_destroy();

// Langkah 4: Arahkan pengguna kembali ke halaman login setelah sesi dihancurkan.
header('Location: login.php');

// Langkah 5: Hentikan eksekusi skrip untuk memastikan tidak ada kode lain yang berjalan
// setelah proses redirect.
exit;