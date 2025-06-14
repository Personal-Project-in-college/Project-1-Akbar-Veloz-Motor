<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
    <main>
        <section class="login-section">
            <div class="login-container">
                <div class="login-header">
                    <img src="./assets/icons/logo.png" alt="Akbar Veloz Motor Logo" />
                    <h1>Login Customer</h1>
                </div>

                <div class="login-body">
                    <div id="auth-message" class="auth-message" style="display: none; margin: 20px 0px;"></div>

                    <form id="login-form">
                        <div class="inputGroup">
                            <input type="text" id="email" name="email" required />
                            <label for="email">Email</label>
                        </div>

                        <div class="inputGroup">
                            <input type="password" id="password" name="password" required />
                            <label for="password">Password</label>
                            <i class="fa fa-eye-slash toggle-password" id="togglePassword"></i>
                        </div>
                        <button type="submit" class="btn-login btn-primary-login" id="loginBtn">Masuk</button>
                    </form>

                    <div class="divider"><span class="divider-text">ATAU</span></div>

                    <div class="social-login">
                        <button class="btn-login btn-social btn-google" id="btn-google">
                            <span><img src="./assets/icons/google-icon.png" alt=""> </span> Masuk dengan Google
                        </button>
                        <button class="btn-login btn-social btn-facebook" id="btn-facebook">
                            <span><img src="./assets/icons/facebook-icon.png" alt=""> Masuk dengan Facebook
                        </button>
                    </div>
                </div>

                <div class="login-footer">
                    Belum punya akun? <a href="register.php">Daftar disini</a><br />
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
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // validasi input
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("password");

            togglePassword.addEventListener("click", () => {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                togglePassword.classList.toggle("fa-eye");
                togglePassword.classList.toggle("fa-eye-slash");
            });

            // login api
            const loginForm = document.getElementById('login-form');
            const loginBtn = document.getElementById('loginBtn');
            const messageDiv = document.getElementById('auth-message'); // Sekarang elemen ini pasti ditemukan
            const googleLoginBtn = document.getElementById('btn-google');
            const facebookLoginBtn = document.getElementById('btn-facebook');

            function showMessage(type, text) {
                // Sekarang messageDiv tidak akan null
                messageDiv.className = `auth-message auth-${type}`; // Menggunakan kelas yang sudah ada
                messageDiv.textContent = text;
                messageDiv.style.display = 'block';
            }

            // PERUBAIKAN 3: Menangani pesan error dari URL saat halaman dimuat
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            if (error) {
                showMessage('error', error);
            }

            // Handle regular login
            loginForm.addEventListener('submit', async (e) => { // Menggunakan event 'submit' pada form
                e.preventDefault();
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                try {
                    const response = await fetch('./api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'login',
                            email,
                            password
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showMessage('success', data.message);
                        setTimeout(() => {
                            // Redirect ke halaman index.php setelah berhasil login
                            window.location.href = 'index.php';
                        }, 1500);
                    } else {
                        showMessage('error', data.message);
                    }
                } catch (err) {
                    showMessage('error', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
                }
            });


            // Handle Google Login
            googleLoginBtn.addEventListener('click', () => {
                window.location.href = './api/auth.php?action=google_login';
            });

            // Facebook Login (placeholder for future)
            facebookLoginBtn.addEventListener('click', () => {
                alert('Fitur ini akan segera dikembangkan!');
            });
        });
    </script>
</body>

</html>