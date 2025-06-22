<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/koneksi.php';

// Load .env config
$env = parse_ini_file(__DIR__ . '/../.env');

function sendEmailToCustomer($toEmail, $toName, $vehicleId, $brand, $model, $typeOrder, $orderDate, $typeArrival, $address, $orderLink)
{
    global $env;

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $env['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $env['MAIL_USERNAME'];
        $mail->Password = $env['MAIL_PASSWORD'];
        $mail->Port = $env['MAIL_PORT'];

        // Sender & recipient
        $mail->setFrom($env['MAIL_FROM_ADDRESS'], $env['MAIL_FROM_NAME']);
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Konfirmasi Pesanan Anda - Akbar Veloz";

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;'>
            <div style='max-width: 600px; margin: auto; background-color: white; border-radius: 10px; overflow: hidden;'>
                <div style='text-align: center; padding: 20px; background-color: #007BFF; color: white;'>
                    <h2>Akbar Veloz</h2>
                </div>
                <div style='padding: 30px;'>
                    <h4 style='text-align:center;'>Terima Kasih, Atas Pesanannya</h4>
                    <p>Hi $toName,</p>
                    <p>Kami menerima permintaan pesanan dari akun Anda dengan rincian sebagai berikut:</p>

                    <table style='width:100%; border-collapse:collapse;'>
                        <tr><td><strong>Kode Kendaraan:</strong></td><td>$vehicleId - $brand - $model</td></tr>
                        <tr><td><strong>Tujuan:</strong></td><td>" . ($typeOrder === 'test_driver' ? 'Uji Coba Kendaraan' : 'Transaksi Kendaraan') . "</td></tr>
                        <tr><td><strong>Jadwal:</strong></td><td>$orderDate WIB</td></tr>
                        <tr><td><strong>Metode Kedatangan:</strong></td><td>" . ($typeArrival === 'home_visit' ? 'Petugas datang ke lokasi saya' : 'Saya akan datang ke showroom') . "</td></tr>
                        <tr><td><strong>Alamat:</strong></td><td>$address</td></tr>
                    </table>

                    <p style='margin-top:20px;'>Anda bisa melihat detail pesanan dengan klik tombol berikut:</p>
                    <div style='text-align:center; margin:20px 0;'>
                        <a href='$orderLink' style='background-color: #007BFF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Pesananku</a>
                    </div>

                    <p style='color:#c0392b;'><strong>Perhatian:</strong> Pesanan hanya bisa dibatalkan dalam waktu kurang dari 5 menit! Segera batalkan jika ada kesalahan dalam pesanan Anda.</p>

                    <p>Untuk bantuan lebih lanjut, Anda dapat menghubungi Dukungan Pelanggan Kami.</p>
                    <p>Untuk memastikan keamanan akun Anda, silahkan baca panduan keamanan kami.</p>
                    <br>
                    <p>Hormat Kami,</p>
                    <p><strong>Akbar Veloz Team</strong></p>
                </div>
            </div>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email gagal dikirim: {$mail->ErrorInfo}");
        return false;
    }
}

