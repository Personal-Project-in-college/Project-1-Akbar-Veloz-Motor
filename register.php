<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
if (isset($_SESSION['customer_id'])) {
    header('Location: index.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrasi - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
    <main>
        <section class="login-section">
            <div class="login-container">
                <div class="login-header">
                    <img src="./assets/icons/logo.png" alt="Akbar Veloz Motor Logo" />
                    <h1>Buat Akun Customer</h1>
                </div>

                <div class="login-body">
                    <div id="auth-message" class="auth-message" style="display: none; margin: 20px 0px"></div>

                    <form id="registerForm">
                        <div class="inputGroup">
                            <input type="name" id="name" name="name" required />
                            <label for="name">Nama</label>
                        </div>

                        <div class="inputGroup">
                            <input type="email" id="email" name="email" required />
                            <label for="email">Email</label>
                        </div>

                        <div class="inputGroup">
                            <input type="password" id="password" name="password" required />
                            <label for="password">Password</label>
                            <i class="fa fa-eye-slash toggle-password" id="togglePassword"></i>
                        </div>

                        <div class="inputGroup">
                            <input type="password" id="confirmPassword" name="confirmPassword" required />
                            <label for="confirmPassword">Konfirmasi Password</label>
                            <i class="fa fa-eye-slash toggle-password" id="toggleConfirmPassword"></i>
                        </div>

                        <button type="submit" class="btn-login btn-primary-login">Daftar</button>
                    </form>

                    <div class="divider"><span class="divider-text">ATAU</span></div>

                    <div class="social-login">
                        <button class="btn-login btn-social btn-google" id="btn-google">
                            <span><img src="./assets/icons/google-icon.png" alt=""> </span> Daftar dengan Google
                        </button>
                        <button class="btn-login btn-social btn-facebook" id="btn-facebook">
                            <span><img src="./assets/icons/facebook-icon.png" alt=""> Daftar dengan Facebook
                        </button>
                    </div>

                    <div class="login-footer">
                        Sudah punya akun? <a href="login.php">Masuk disini</a><br />
                        <a href="#">Lupa password?</a><br />
                        <a href="index.php" class="btn-back">
                            <svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z"></path>
                                <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z"></path>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Fungsi untuk toggle password visibility
            function setupPasswordToggle(toggleId, inputId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                if (toggle && input) {
                    toggle.addEventListener("click", () => {
                        const type = input.getAttribute("type") === "password" ? "text" : "password";
                        input.setAttribute("type", type);
                        toggle.classList.toggle("fa-eye");
                        toggle.classList.toggle("fa-eye-slash");
                    });
                }
            }

            setupPasswordToggle("togglePassword", "password");
            setupPasswordToggle("toggleConfirmPassword", "confirmPassword");

            // Elemen-elemen form
            const registerForm = document.getElementById("registerForm");
            const messageDiv = document.getElementById('auth-message');
            const googleBtn = document.getElementById('btn-google');
            const facebookBtn = document.getElementById('btn-facebook');

            // Fungsi untuk menampilkan pesan
            function showMessage(type, text) {
                messageDiv.className = `auth-message auth-${type}`;
                messageDiv.textContent = text;
                messageDiv.style.display = 'block';
            }

            // Menangani error dari URL (jika ada)
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            if (error) {
                showMessage('error', error);
            }

            // Event listener untuk form registrasi
            registerForm.addEventListener("submit", async function(e) {
                e.preventDefault();

                // Mengambil nilai dari form
                const email = document.getElementById("email").value;
                const password = document.getElementById("password").value;
                const name = document.getElementById("name").value;
                const confirmPassword = document.getElementById("confirmPassword").value;

                // Validasi password di sisi klien
                if (password !== confirmPassword) {
                    showMessage("error", "Password dan Konfirmasi Password tidak cocok.");
                    return;
                }

                // Kirim data ke API backend
                try {
                    const response = await fetch('./api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'register',
                            name: name,
                            email: email,
                            password: password
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showMessage('success', data.message);
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000);
                        }
                    } else {
                        showMessage('error', data.message);
                    }
                }catch (err) {
                    showMessage('error', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
                }
            });

            // Handle Google Login/Register
            googleBtn.addEventListener('click', () => {
                window.location.href = './api/auth.php?action=google_login';
            });

            // Facebook Login (placeholder for future)
            facebookBtn.addEventListener('click', () => {
                alert('Fitur ini akan segera dikembangkan!');
            });
        });
    </script>
</body>

</html>