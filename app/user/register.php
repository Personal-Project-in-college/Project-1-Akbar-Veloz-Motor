<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT");
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
    <main class="container">
        <section class="login-section">
            <div class="login-container">
                <div class="login-header">
                    <img src="./assets/icons/logo.png" alt="Akbar Veloz Motor Logo" />
                    <h1>Buat Akun Customer</h1>
                </div>

                <div class="login-body">
                    <form id="registerForm">
                        <div class="inputGroup">
                            <input type="text" id="email" name="email" required />
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
                        <button class="btn-login btn-social btn-google">
                            <span><img src="./assets/icons/google-icon.png" alt=""> </span> Masuk dengan Google
                        </button>
                        <button class="btn-login btn-social btn-facebook">
                            <span><img src="./assets/icons/facebook-icon.png" alt=""> Masuk dengan Facebook
                        </button>
                    </div>
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
        </section>
    </main>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", () => {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            togglePassword.classList.toggle("fa-eye");
            togglePassword.classList.toggle("fa-eye-slash");
        });

        const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
        const confirmPasswordInput = document.getElementById("confirmPassword");

        toggleConfirmPassword.addEventListener("click", () => {
            const type = confirmPasswordInput.getAttribute("type") === "password" ? "text" : "password";
            confirmPasswordInput.setAttribute("type", type);
            toggleConfirmPassword.classList.toggle("fa-eye");
            toggleConfirmPassword.classList.toggle("fa-eye-slash");
        });

        document.getElementById("registerForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            if (password !== confirmPassword) {
                alert("Password dan Konfirmasi Password tidak cocok.");
                return;
            }

            console.log("Register with:", email, password);
            // Lanjutkan ke backend handling
        });
    </script>
</body>

</html>