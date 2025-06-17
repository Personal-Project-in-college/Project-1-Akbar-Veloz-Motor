<?php

/**
 * File: login.php
 * Halaman ini menangani proses otentikasi pengguna.
 * - Menampilkan form login.
 * - Memvalidasi kredensial untuk login standar (redirect) dan login AJAX (JSON response).
 * - Mengarahkan pengguna yang sudah login agar tidak bisa mengakses halaman ini lagi.
 */

// Memulai sesi untuk penggunaan variabel $_SESSION.
session_start();

// Jika pengguna sudah login, langsung arahkan ke dashboard.
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

// Mengimpor file koneksi database.
include '../../../../config/koneksi.php';

// Proses login hanya jika form dikirim menggunakan metode POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // =================================================================
    // BAGIAN BARU: LOGIKA UNTUK LOGIN RAMAH LIVE CHAT (AJAX)
    // =================================================================
    // Kita cek apakah ada parameter 'action' dengan nilai 'ajax_login'
    if (isset($_POST['action']) && $_POST['action'] == 'ajax_login') {
        // Set header untuk memberitahu client bahwa responsnya adalah JSON
        header('Content-Type: application/json');

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validasi input tidak boleh kosong
        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username dan password tidak boleh kosong.']);
            exit();
        }

        try {
            // Mengambil data user dari tabel 'users' (bukan 'admins')
            $stmt = $koneksi->prepare("SELECT users.*, roles.name AS role_name FROM users JOIN roles ON users.role_id = roles.id WHERE users.username = ? AND users.deleted_at IS NULL");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifikasi user ditemukan dan password cocok
            if ($user && password_verify($password, $user['password'])) {
                // Regenerasi session ID untuk keamanan
                session_regenerate_id(true);

                // Simpan informasi penting ke dalam session
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['name']      = $user['name'];
                $_SESSION['role_name'] = $user['role_name'];
                $_SESSION['role_id'] = $user['role_id']; // ✅ Tidak undefined lagi
                $_SESSION['photo']     = $user['photo'];
                $_SESSION['phone']     = $user['phone'];

                // Perbarui status online dan waktu aktivitas terakhir pengguna
                $stmt_update_online = $koneksi->prepare("UPDATE users SET is_online = TRUE, last_activity = NOW() WHERE id = ?");
                $stmt_update_online->execute([$user['id']]);

                // Kirim respons sukses dalam format JSON
                echo json_encode(['success' => true, 'message' => 'Login berhasil! Anda akan diarahkan...']);
            } else {
                // Kirim respons gagal dalam format JSON
                echo json_encode(['success' => false, 'message' => 'Username atau password yang Anda masukkan salah.']);
            }
        } catch (PDOException $e) {
            // Catat error ke log server (lebih aman daripada menampilkannya ke user)
            error_log("Login AJAX database error: " . $e->getMessage());
            // Kirim respons error database dalam format JSON
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.']);
        }
        // Hentikan eksekusi script agar tidak menampilkan HTML di bawah
        exit();
    }

    // =================================================================
    // BAGIAN LAMA: LOGIKA UNTUK LOGIN STANDAR (FORM BIASA)
    // =================================================================
    else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = $koneksi->prepare("SELECT users.*, roles.name AS role_name FROM users JOIN roles ON users.role_id = roles.id WHERE users.username = ? AND users.deleted_at IS NULL");
        $query->execute([$username]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['name']      = $user['name'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['role_id'] = $user['role_id']; // ✅ Tidak undefined lagi
            $_SESSION['photo']     = $user['photo'];
            $_SESSION['phone']     = $user['phone'];

            // Di sini juga, kita update status online saat login standar
            $stmt_update_online = $koneksi->prepare("UPDATE users SET is_online = TRUE, last_activity = NOW() WHERE id = ?");
            $stmt_update_online->execute([$user['id']]);

            header('Location: ../dashboard/index.php');
            exit;
        } else {
            $error = "Username atau password yang Anda masukkan salah!";
        }
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

            <form id="loginForm" method="POST" action="login.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <?php
                // Pesan error ini utamanya untuk login standar (non-AJAX)
                if (isset($error)) {
                    echo "<p class='error-message' style='color: red;'>{$error}</p>";
                }
                ?>
                <div id="ajax-error-message" style="color: red; display: none;"></div>

                <button type="submit">LOGIN</button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            // Mencegah form mengirim data secara tradisional
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            // Menambahkan parameter 'action' untuk memicu logika AJAX di PHP
            formData.append('action', 'ajax_login');

            const errorMessageDiv = document.getElementById('ajax-error-message');
            errorMessageDiv.style.display = 'none'; // Sembunyikan pesan error lama

            fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Jika login sukses, tampilkan pesan dan arahkan ke dashboard
                        alert(data.message);
                        window.location.href = '../dashboard/index.php';
                    } else {
                        // Jika gagal, tampilkan pesan error dari server
                        errorMessageDiv.textContent = data.message;
                        errorMessageDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    // Jika ada error jaringan atau parsing JSON
                    console.error('Error:', error);
                    errorMessageDiv.textContent = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                    errorMessageDiv.style.display = 'block';
                });
        });
    </script>

</body>

</html>