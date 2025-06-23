
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/koneksi.php';

$env = parse_ini_file(__DIR__ . '/../.env');

function sendEmailAfterVerified($toEmail, $toName, $plainPassword)
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
        $mail->Subject = "Selamat! Akun Anda Berhasil Diverifikasi - Akbar Veloz";

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;'>
            <div style='max-width: 600px; margin: auto; background-color: white; border-radius: 10px; overflow: hidden;'>
                <div style='text-align: center; padding: 20px; background-color: #007BFF; color: white;'>
                    <h2>Akbar Veloz</h2>
                </div>
                <div style='padding: 30px;'>
                    <h4 style='text-align:center;'>Registrasi Berhasil!</h4>
                    <p>Hi $toName,</p>
                    <p>Selamat! Akun kamu berhasil dibuat dan diverifikasi. Berikut informasi akun kamu:</p>

                    <table style='width:100%; border-collapse:collapse;'>
                        <tr><td><strong>Email:</strong></td><td>$toEmail</td></tr>
                        <tr>
                            <td><strong>Password:</strong></td>
                            <td>
                                <button onclick=\"navigator.clipboard.writeText('$plainPassword')\" 
                                    style=\"padding: 5px 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;\">
                                    Copy Password
                                </button>
                            </td>
                        </tr>
                    </table>

                    <div style='text-align:center; margin:30px 0;'>
                        <a href='http://project-1-akbar-veloz-motor.com/login.php' style='display:inline-block; background-color:#007BFF; color:white; padding:12px 24px; border-radius:8px; text-decoration:none; font-size:16px;'>Login Sekarang</a>
                    </div>

                    <p>Terima kasih telah bergabung bersama kami.</p>
                    <br>
                    <p>Hormat Kami,</p>
                    <p><strong>Tim Akbar Veloz</strong></p>
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
