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
    <title>Verifikasi OTP - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <main>
        <section class="login-section">
            <div class="login-container">
                <div class="login-header">
                    <img src="./assets/icons/logo.png" alt="Logo Akbar Veloz Motor">
                    <h1>Verifikasi Kode OTP</h1>
                    <p>Masukkan 5 digit kode yang telah dikirim ke email kamu.</p>
                </div>
                <div class="login-body">
                    <div id="otp-message" class="auth-message" style="display: none; margin: 20px 0px;"></div>
                    <form id="otpForm">
                        <div class="inputGroup">
                            <input type="text" id="otp_code" name="otp_code" maxlength="5" pattern="\d{5}" required autocomplete="off" />
                            <label for="otp_code">Kode OTP</label>
                        </div>
                        <button type="submit" class="btn-login btn-primary-login">Verifikasi</button>
                        <button type="button" id="resendOtpBtn" class="btn-login btn-secondary-login" style="margin-top: 10px;">
                            Kirim Ulang OTP
                        </button>
                        <span id="resendTimer" style="display:none; font-size: 0.9rem; margin-top: 20px; margin-bottom: 5px; color: gray; justify-content: center;">Tunggu 60 detik...</span>
                    </form>
                    <div class="login-footer">
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
            const otpForm = document.getElementById('otpForm');
            const messageDiv = document.getElementById('otp-message');
            const resendBtn = document.getElementById('resendOtpBtn');
            const timerText = document.getElementById('resendTimer');

            // Cek status resend_count saat halaman dimuat
            (async function checkOtpStatus() {
                try {
                    const response = await fetch('./api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'check_otp_status'
                        })
                    });

                    const data = await response.json();
                    if (data.success && data.resend_count >= 3) {
                        resendBtn.style.display = 'none';
                        timerText.textContent = 'Batas kirim ulang OTP telah tercapai.';
                        timerText.style.display = 'flex';
                        timerText.style.color = 'red';
                    }
                } catch (error) {
                    console.warn('Gagal cek status OTP:', error);
                }
            })();


            let timer;

            function showMessage(type, text) {
                messageDiv.className = `auth-message auth-${type}`;
                messageDiv.textContent = text;
                messageDiv.style.display = 'block';
            }

            otpForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const otp = document.getElementById("otp_code").value;

                try {
                    const response = await fetch('./api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'verify_otp',
                            otp: otp
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showMessage('success', data.message);
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showMessage('error', data.message);
                    }
                } catch (err) {
                    showMessage('error', 'Gagal memverifikasi kode. Coba lagi.');
                }
            });

            function startResendCooldown(seconds) {
                resendBtn.disabled = true;
                timerText.style.display = 'flex';
                timerText.style.marginTop = '20px';
                let countdown = seconds;

                timerText.textContent = `Tunggu ${countdown} detik...`;
                timer = setInterval(() => {
                    countdown--;
                    if (countdown <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        timerText.style.display = 'none';
                    } else {
                        timerText.textContent = `Tunggu ${countdown} detik...`;
                    }
                }, 1000);
            }

            resendBtn.addEventListener('click', async () => {
                try {
                    const response = await fetch('./api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'resend_otp'
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        showMessage('success', data.message);
                        startResendCooldown(60); // 60 detik cooldown
                    } else {
                        showMessage('error', data.message);
                    }
                } catch (err) {
                    showMessage('error', 'Gagal mengirim ulang OTP.');
                }
            });
        });
    </script>
</body>

</html>