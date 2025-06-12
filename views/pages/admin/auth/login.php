<?php

/**
 * File: login.php
 * Halaman ini menangani proses otentikasi pengguna.
 * - Menampilkan form login.
 * - Memvalidasi kredensial (username & password) saat form disubmit.
 * - Mengarahkan pengguna yang sudah login agar tidak bisa mengakses halaman ini lagi.
 */

// Memulai sesi untuk penggunaan variabel $_SESSION.
session_start();

// Jika pengguna sudah login, langsung arahkan ke dashboard.
// Mencegah pengguna yang sudah login melihat halaman ini lagi.
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

// Mengimpor file koneksi database.
include '../../../../config/koneksi.php';

// Proses login hanya jika form dikirim menggunakan metode POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Ambil data user dari database berdasarkan username.
    // Query ini juga menggabungkan tabel 'roles' untuk mendapatkan nama role.
    // Dan memastikan user yang login tidak dalam status soft-deleted.
    $query = $koneksi->prepare("SELECT users.*, roles.name AS role_name FROM users JOIN roles ON users.role_id = roles.id WHERE users.username = ? AND users.deleted_at IS NULL");
    $query->execute([$username]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // 2. Verifikasi password.
    // Pengecekan: apakah user ditemukan DAN password yang diinput sesuai dengan hash di database.
    if ($user && password_verify($password, $user['password'])) {
        // Jika berhasil, simpan informasi penting ke dalam session.
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['name']      = $user['name'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['photo']     = $user['photo']; // 👉 Tambahkan ini



        // Arahkan ke halaman dashboard.
        header('Location: ../dashboard/index.php');
        exit;
    } else {
        // Jika gagal, siapkan pesan error untuk ditampilkan di form.
        $error = "Username atau password yang Anda masukkan salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AKBAR VELOZ MOTOR</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    <main class="container">
        <div class="image-container">
            <img src="../assets/images/login.png" alt="Akbar Veloz Motor">
        </div>
        <div class="form-container">
            <h1>AKBAR VELOZ MOTOR</h1>
            <h2>Selamat Datang!</h2>
            <p>Silahkan login ke dalam akunmu</p>

            <form method="POST" action="">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <?php
                // Tampilkan pesan error jika variabel $error ada (artinya login gagal).
                if (isset($error)) {
                    echo "<p class='error-message' style='color: red;'>{$error}</p>";
                }
                ?>

                <button type="submit">LOGIN</button>
            </form>
        </div>
    </main>
</body>

</html>