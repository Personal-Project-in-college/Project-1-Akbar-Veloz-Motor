<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/koneksi.php';

// Load .env config
$env = parse_ini_file(__DIR__ . '/../.env');

function sendTransactionFinishEmailCustomer($toEmail, $toName, $vehicleId, $brand, $model, $transactionId, $priceDisplayed, $dealNegotiation, $grandTotal, $paymentType, $downPayment, $remainingAmount, $paymentMethod, $status, $detailLink)
{
    global $env;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $env['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $env['MAIL_USERNAME'];
        $mail->Password = $env['MAIL_PASSWORD'];
        $mail->Port = $env['MAIL_PORT'];

        $mail->setFrom($env['MAIL_FROM_ADDRESS'], $env['MAIL_FROM_NAME']);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = "Transaksi Selesai - Akbar Veloz";

        // Ubah enum status ke bahasa Indonesia
        $statusMap = [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'dp_paid' => 'DP Sudah Dibayar',
            'cancelled' => 'Dibatalkan',
            'failed' => 'Gagal',
        ];
        $statusText = $statusMap[$status] ?? $status;

        $dpRow = '';
        $remainingRow = '';
        if ($paymentType === 'cicilan') {
            $dpRow = "<tr><td><strong>Uang DP:</strong></td><td>Rp " . number_format($downPayment, 0, ',', '.') . "</td></tr>";
            $remainingRow = "<tr><td><strong>Sisa Uang:</strong></td><td>Rp " . number_format($remainingAmount, 0, ',', '.') . "</td></tr>";
        }

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;'>
            <div style='max-width: 600px; margin: auto; background-color: white; border-radius: 10px; overflow: hidden;'>
                <div style='text-align: center; padding: 20px; background-color: #28a745; color: white;'>
                    <h2>Akbar Veloz</h2>
                </div>
                <div style='padding: 30px;'>
                    <h4 style='text-align:center;'>Transaksi Anda Telah Selesai</h4>
                    <p>Hi $toName,</p>
                    <p>Kami telah menyelesaikan proses transaksi Anda dengan rincian sebagai berikut:</p>

                    <table style='width:100%; border-collapse:collapse;'>
                        <tr><td><strong>Kode Kendaraan:</strong></td><td>$vehicleId - $brand - $model</td></tr>
                        <tr><td><strong>ID Transaksi:</strong></td><td>$transactionId</td></tr>
                        <tr><td><strong>Harga Kendaraan:</strong></td><td>Rp " . number_format($priceDisplayed, 0, ',', '.') . "</td></tr>
                        <tr><td><strong>Deal Negoisasi:</strong></td><td>Rp " . number_format($dealNegotiation, 0, ',', '.') . "</td></tr>
                        <tr><td><strong>Grand Total:</strong></td><td>Rp " . number_format($grandTotal, 0, ',', '.') . "</td></tr>
                        <tr><td><strong>Jenis Pembayaran:</strong></td><td>" . ucfirst($paymentType) . "</td></tr>
                        $dpRow
                        $remainingRow
                        <tr><td><strong>Metode Pembayaran:</strong></td><td>" . ucfirst($paymentMethod) . "</td></tr>
                        <tr><td><strong>Status:</strong></td><td>$statusText</td></tr>
                    </table>

                    <p style='margin-top:20px;'>Klik tombol di bawah untuk melihat detail transaksi Anda:</p>
                    <div style='text-align:center; margin:20px 0;'>
                        <a href='$detailLink' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Lihat Transaksi</a>
                    </div>

                    <p>Jika ada pertanyaan atau butuh bantuan, hubungi tim dukungan kami.</p>
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
