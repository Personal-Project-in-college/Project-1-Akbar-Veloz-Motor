<?php
include '../config/koneksi.php';

function storeCustomerOTP($customer_id, $otp_code)
{
    global $koneksi;
    $expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $now = date('Y-m-d H:i:s');

    // Cek apakah customer sudah punya OTP sebelumnya
    $cek = $koneksi->prepare("SELECT * FROM customer_otps WHERE customer_id = ?");
    $cek->execute([$customer_id]);
    $existing = $cek->fetch();

    if ($existing) {
        // Update resend_count dan OTP baru
        $resend_count = (int)$existing['resend_count'] + 1;
        $stmt = $koneksi->prepare("UPDATE customer_otps SET otp_code = ?, expired_at = ?, resend_count = ?, last_sent_at = ? WHERE customer_id = ?");
        $stmt->execute([$otp_code, $expired_at, $resend_count, $now, $customer_id]);
    } else {
        // Insert pertama kali
        $stmt = $koneksi->prepare("INSERT INTO customer_otps (customer_id, otp_code, expired_at, resend_count, last_sent_at) VALUES (?, ?, ?, 0, ?)");
        $stmt->execute([$customer_id, $otp_code, $expired_at, $now]);
    }
}
