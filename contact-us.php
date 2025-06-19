<?php
require_once 'config/koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['customer_id']) || empty($_SESSION['customer_id'])) {
  $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
  $message = urlencode("Anda harus login terlebih dahulu untuk mengakses halaman tersebut.");
  header("Location: login.php?alert_message=" . $message);
  exit();
}


// Load data customer
$customer_id = $_SESSION['customer_id'];
$customer_name = '';
$customer_email = '';

// Cek apakah ada pesanan yang statusnya masih process
$cekPesanan = $koneksi->prepare("SELECT id FROM orders WHERE customer_id = ? AND status = 'proced' LIMIT 1");
$cekPesanan->execute([$customer_id]);
$pesananAktif = $cekPesanan->fetch(PDO::FETCH_ASSOC);

if ($pesananAktif) {
  header("Location: detail-pesanan.php?id=" . $pesananAktif['id']);
  exit();
}

try {
  $stmt = $koneksi->prepare("SELECT name, email FROM customers WHERE id = ?");
  $stmt->execute([$customer_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $customer_name = $user['name'];
    $customer_email = $user['email'];
  }
} catch (PDOException $e) {
  die("Error saat mengambil data pengguna: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['pending_order'] = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
    'address' => $_POST['address'],
    'vehicle_id' => $_POST['vehicle'],
    'type_order' => $_POST['type_order'],
    'type_arrival' => $_POST['type_arrival'],
    'order_date' => $_POST['order_date'] ?? date('Y-m-d H:i:s')
  ];

  header("Location: preview-detail-pesanan.php?preview=true");
  exit();
}


$vehiclesQuery = $koneksi->query("SELECT v.id, vm.name AS model_name FROM vehicles AS v JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id WHERE v.status = 'available' AND v.deleted_at IS NULL");
$vehicles = $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id" translate="no">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Drive - Akbar Veloz Motor</title>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- CSS Choices -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

  <!-- JS Choices -->
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

</head>

<body>
  <!-- Navbar -->
  <?php include("./layouts/navbar.php"); ?>


  <main class="container">
    <section class="test-drive-form">
      <!-- <h2>Hubungi Kami untuk Transaksi atau Test Drive</h2> -->
      <h2>Hubungi Kami</h2>
      <div>

        <form method="POST" id="testDriveForm" class="testDriveForm">
          <div class="testDrive-container">

            <div class="inputGroup">
              <input type="text" id="name" name="name" required autocomplete="off"
                value="<?php echo htmlspecialchars($customer_name); ?>"
                <?php if (!empty($customer_name)); ?>>
              <label for="name">Name</label>
            </div>

            <div class="inputGroup">
              <input type="tel" id="whatsapp" name="phone" required autocomplete="off">
              <label for="phonr">Nomor WhatsApp Aktif</label>
            </div>

            <div class="inputGroup">
              <textarea id="address" name="address" rows="14" required></textarea>
              <label for="address">Alamat</label>
            </div>
          </div>

          <div class="testDrive-container">
            <div class="inputGroup">
              <input type="email" id="email" name="email" required autocomplete="off" readonly
                value="<?php echo htmlspecialchars($customer_email);  ?>"
                <?php if (!empty($customer_email)); ?>>
              <label for="email">Email</label>
            </div>

            <div class="select-wrapper">
              <select class="modern-select" id="vehicle" name="vehicle" required>
                <option value="">-- Pilih Kendaraan --</option>
                <?php foreach ($vehicles as $vehicle): ?>
                  <option value="<?= $vehicle['id'] ?>">
                    <?= $vehicle['id'] ?> | <?= htmlspecialchars($vehicle['model_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="select-wrapper">
              <select class="modern-select" id="purpose" name="type_order" required>
                <option value="">-- Tentukan tujuan --</option>
                <option value="test_driver">Coba Kendaraan</option>
                <option value="transaction">Transaksi</option>
              </select>
            </div>

            <div class="select-wrapper">
              <select class="modern-select" id="arrival_method" name="type_arrival" required>
                <option value="">-- Tentukan Kedatangan --</option>
                <option value="showroom">Saya akan datang ke showroom</option>
                <option value="home_visit">Saya ingin petugas datang ke lokasi saya</option>
              </select>
            </div>

            <div class="datepicker-wrapper">
              <label for="date">Tentukan Jadwal</label>
              <div class="datepicker-container">
                <input class="modern-datepicker" type="datetime-local" id="date" name="order_date" required>
                <div class="datepicker-icon">
                  <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" fill="currentColor" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

      </div>
      <div class="form-actions">
        <a href="index.php" class="btn-secondary">Kembali</a>
        <button type="submit" class="btn">Lanjut</button>
        <!-- <button type="submit" class="btn">
          Kirim
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M20 4L3 9.31372L10.5 13.5M20 4L14.5 21L10.5 13.5M20 4L10.5 13.5" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg>
        </button> -->
      </div>
      </form>
    </section>
  </main>

  <!-- Footer -->
  <?php include("./layouts/footer.php"); ?>


  <script src="./js/global.js"></script>
  <script src="./js/contact-us.js"></script>

</body>

</html>