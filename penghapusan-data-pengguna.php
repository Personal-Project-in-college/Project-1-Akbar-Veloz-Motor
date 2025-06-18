<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Penghapusan Data Pengguna - Akbar Veloz Motor</title>
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap");

      :root {
        --primary-color: #1a237e;
        --secondary-color: #ff6d00;
        --text-color: #333;
        --highlight-color: #fff3cd;
        --accent-color: #ffab40;
        --light-color: #f5f5f5;
        --black: black;
        --dark-color: #212121;
        --gray-color: #757575;
        --white-color: #ffffff;
        --success-color: #4caf50;
        --error-color: #f44336;
        --gray: #dddddd;
        --dark-gray: #777777;
        --gray: #dddddd;
        --dark-gray: #777777;
        --border-radius: 5px;
      }

      body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: var(--text-color);
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
      }

      .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: var(--border-radius);
        margin-top: 30px;
        margin-bottom: 30px;
      }

      header {
        background-color: var(--primary-color);
        color: white;
        padding: 20px 0;
        text-align: center;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        margin-bottom: 30px;
      }

      h1 {
        margin: 0;
        font-size: 28px;
        color: white;
      }

      h2 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 5px;
        margin-top: 25px;
      }

      .highlight {
        background-color: var(--highlight-color);
        padding: 2px 5px;
        border-radius: 3px;
        font-weight: bold;
      }

      ul,
      ol {
        padding-left: 20px;
      }

      li {
        margin-bottom: 10px;
      }

      p {
        margin-bottom: 15px;
      }

      .info-box {
        background-color: var(--light-color);
        padding: 15px;
        border-left: 4px solid var(--accent-color);
        margin: 20px 0;
        border-radius: 0 var(--border-radius) var(--border-radius) 0;
      }

      .warning-box {
        background-color: #fff3cd;
        padding: 15px;
        border-left: 4px solid #ffc107;
        margin: 20px 0;
        border-radius: 0 var(--border-radius) var(--border-radius) 0;
      }

      .back-links {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
      }

      .back-links a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: bold;
        padding: 8px 15px;
        border: 1px solid var(--primary-color);
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
      }

      .back-links a:hover {
        background-color: var(--primary-color);
        color: white;
      }

      .logo {
        max-height: 60px;
        margin-bottom: 15px;
      }

      @media (max-width: 768px) {
        .container {
          margin: 15px;
          padding: 15px;
        }

        h1 {
          font-size: 24px;
        }

        .back-links {
          flex-direction: column;
          gap: 10px;
        }

        .back-links a {
          text-align: center;
        }
      }

       .wrap-img  {
        width: 100%;
        display: flex;
        justify-content: center;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="wrap-img">
    <img
          src="./assets/icons/logo.png"
          alt="Logo Akbar Veloz Motor"
          class="logo"
        />
      </div>

      <header>
        <h1>Kebijakan Privasi Akbar Veloz Motor</h1>
      </header>

      <div class="info-box">
        <p>
          Akbar Veloz Motor berkomitmen untuk melindungi privasi data pelanggan.
          Jika Anda ingin data pribadi Anda dihapus dari sistem kami, silakan
          ikuti panduan di bawah ini.
        </p>
      </div>

      <h2>Informasi yang Kami Simpan</h2>
      <p>
        Ketika Anda menggunakan layanan kami, baik melalui login dengan
        Google/Facebook maupun pendaftaran manual, kami menyimpan informasi
        berikut:
      </p>
      <ul>
        <li>
          <strong>Data Pribadi:</strong> Nama lengkap, alamat email, nomor
          telepon
        </li>
        <li>
          <strong>Data Akun:</strong> ID unik dari Google/Facebook (jika login
          menggunakan layanan tersebut), password terenkripsi (untuk pendaftaran
          manual)
        </li>
        <li>
          <strong>Data Servis:</strong> Riwayat servis kendaraan, catatan
          teknis, dan informasi kendaraan
        </li>
        <li>
          <strong>Data Transaksi:</strong> Riwayat pembelian suku cadang atau
          layanan
        </li>
      </ul>

      <h2>Cara Meminta Penghapusan Data</h2>
      <p>Untuk meminta penghapusan data pribadi Anda, ikuti langkah berikut:</p>
      <ol>
        <li>
          <strong>Kirim Permintaan Resmi</strong>
          <p>
            Kirim email ke
            <span class="highlight">cs@akbarvelozmotor.com</span> dengan subjek
            "Permintaan Penghapusan Data". Lampirkan:
          </p>
          <ul>
            <li>
              Foto KTP/SIM yang masih berlaku (untuk verifikasi identitas)
            </li>
            <li>Alamat email/nomor telepon yang terdaftar di sistem kami</li>
            <li>Nomor polisi kendaraan (jika terkait data servis)</li>
            <li>Pernyataan jelas bahwa Anda ingin menghapus data Anda</li>
          </ul>
        </li>
        <li>
          <strong>Permintaan Langsung di Bengkel</strong>
          <p>Anda dapat datang langsung ke bengkel kami dengan membawa:</p>
          <ul>
            <li>KTP/SIM asli</li>
            <li>Buku servis kendaraan (jika terkait data servis)</li>
          </ul>
        </li>
      </ol>

      <div class="warning-box">
        <h3>Penting!</h3>
        <p>Penghapusan data akan mempengaruhi:</p>
        <ul>
          <li>Riwayat garansi servis kendaraan Anda</li>
          <li>Kemampuan untuk melacak riwayat perawatan kendaraan</li>
          <li>Promo dan benefit pelanggan yang mungkin Anda terima</li>
        </ul>
      </div>

      <h2>Proses Penghapusan Data</h2>
      <p>Setelah permintaan diverifikasi, kami akan:</p>
      <ul>
        <li>
          Menghapus data pribadi Anda dari sistem aktif dalam
          <span class="highlight">7 hari kerja</span>
        </li>
        <li>
          Mengarsipkan data transaksi dan servis (tanpa identitas pribadi) untuk
          kepatuhan pajak dan regulasi
        </li>
        <li>Mengirim konfirmasi via email setelah proses selesai</li>
      </ul>
      <p>
        Data mungkin tetap ada dalam backup sistem selama maksimal
        <span class="highlight">90 hari</span> sebelum dihapus permanen.
      </p>

      <h2>Pencabutan Akses Akun Sosial</h2>
      <p>
        Jika Anda login menggunakan Google/Facebook, Anda bisa mencabut akses
        aplikasi kami:
      </p>
      <ul>
        <li>
          <strong>Google:</strong>
          <a href="https://myaccount.google.com/connections" target="_blank"
            >Pengaturan Keamanan Akun Google</a
          >
        </li>
        <li>
          <strong>Facebook:</strong>
          <a
            href="https://www.facebook.com/settings?tab=applications"
            target="_blank"
            >Pengaturan Aplikasi Facebook</a
          >
        </li>
      </ul>

      <div class="info-box">
        <h3>Layanan Pelanggan</h3>
        <p>Untuk pertanyaan lebih lanjut, hubungi:</p>
        <ul>
          <li>Telepon: (021) 1234-5678</li>
          <li>WhatsApp: 0812-3456-7890</li>
          <li>Email: cs@akbarvelozmotor.com</li>
          <li>Jam Operasional: Senin-Sabtu, 08:00-17:00 WIB</li>
        </ul>
      </div>

      <div class="back-links">
        <a href="dashboard.php">Kembali ke Dashboard</a>
        <a href="index.php">Kembali ke Beranda</a>
      </div>
    </div>
  </body>
</html>
