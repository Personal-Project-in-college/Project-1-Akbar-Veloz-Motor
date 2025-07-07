<?php
date_default_timezone_set('Asia/Jakarta');
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

$selected_vehicle = $_GET['vehicle_id'] ?? '';
$selected_order_type = $_GET['type_order'] ?? '';
$negotiated_price_from_chat = $_GET['negotiated_price'] ?? null;

$customer_id = $_SESSION['customer_id'];
$customer_name = '';
$customer_email = '';
$customer_phone = '';
$customer_address = '';

$cekPesanan = $koneksi->prepare("SELECT id FROM orders WHERE customer_id = ? AND status = 'proced' LIMIT 1");
$cekPesanan->execute([$customer_id]);
$pesananAktif = $cekPesanan->fetch(PDO::FETCH_ASSOC);

if ($pesananAktif) {
  header("Location: detail-pesanan.php?id=" . $pesananAktif['id']);
  exit();
}

try {
  $stmt = $koneksi->prepare("SELECT name, email, phone, address FROM customers WHERE id = ?");
  $stmt->execute([$customer_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $customer_name = $user['name'];
    $customer_email = $user['email'];
    $customer_phone = $user['phone'];
    $customer_address = $user['address'];
  }
} catch (PDOException $e) {
  die("Error saat mengambil data pengguna: " . $e->getMessage());
}

if (!empty($selected_vehicle) && !empty($selected_order_type)) {
  $_SESSION['pending_order'] = [
    'name' => $customer_name,
    'email' => $customer_email,
    'phone' => $customer_phone,
    'address' => $customer_address,
    'vehicle_id' => $selected_vehicle,
    'type_order' => $selected_order_type,
    'type_arrival' => '',
    'order_date' => '', // Order date dari GET akan ditangani di bawah jika ada
    'negotiated_price' => $negotiated_price_from_chat,
  ];
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
    'order_date' => $_POST['order_date'], // Nilai dari input Flatpickr
    'negotiated_price' => $_SESSION['pending_order']['negotiated_price'] ?? null,
  ];

  header("Location: preview-detail-pesanan.php?preview=true");
  exit();
}

$vehiclesQuery = $koneksi->query("SELECT v.id, vm.name AS model_name FROM vehicles AS v JOIN vehicle_models AS vm ON v.vehicle_model_id = vm.id WHERE v.status = 'available' AND v.deleted_at IS NULL");
$vehicles = $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC);

$form_name = $_SESSION['pending_order']['name'] ?? $customer_name;
$form_email = $_SESSION['pending_order']['email'] ?? $customer_email;
$form_phone = $_SESSION['pending_order']['phone'] ?? $customer_phone;
$form_address = $_SESSION['pending_order']['address'] ?? $customer_address;
$form_vehicle_id = $_SESSION['pending_order']['vehicle_id'] ?? $selected_vehicle;
$form_type_order = $_SESSION['pending_order']['type_order'] ?? $selected_order_type;
$form_type_arrival = $_SESSION['pending_order']['type_arrival'] ?? '';

// Ambil nilai order_date dari sesi atau parameter GET
$form_order_date = $_SESSION['pending_order']['order_date'] ?? ($_GET['order_date'] ?? '');

// Format tanggal untuk Flatpickr (Y-m-d H:i)
if (!empty($form_order_date) && strtotime($form_order_date)) {
  $form_order_date = date('Y-m-d H:i', strtotime($form_order_date));
} else {
  $form_order_date = '';
}
?>

<!DOCTYPE html>
<html lang="id" translate="no">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Drive - Akbar Veloz Motor</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <style>
    /* hilangin icon dropdown bawaan datetime-local */
    input[type="datetime-local"] {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      position: relative;
      background-color: white;
    }

    input[type="datetime-local"]::-webkit-calendar-picker-indicator {
      display: none;
      -webkit-appearance: none;
    }
  </style>

</head>

<body>
  <?php include("./layouts/navbar.php"); ?>


  <main class="container">
    <section class="test-drive-form">
      <h2>Hubungi Kami</h2>
      <div>

        <form method="POST" id="testDriveForm" class="testDriveForm" novalidate>
          <div class="testDrive-container">

            <div class="inputGroup">
              <input type="text" id="name" name="name" required autocomplete="off"
                value="<?php echo htmlspecialchars($form_name); ?>">
              <label for="name">Name</label>
            </div>

            <div class="inputGroup">
              <input type="email" id="email" name="email" required autocomplete="off" readonly
                value="<?php echo htmlspecialchars($form_email); ?>">
              <label for="email">Email</label>
            </div>

            <div class="inputGroup">
              <input type="tel" id="whatsapp" name="phone" required autocomplete="off"
                value="<?php echo htmlspecialchars($form_phone); ?>">
              <label for="phonr">Nomor WhatsApp Aktif</label>
            </div>
            <div class="inputGroup">
              <!-- <textarea id="address" name="address" rows="14" required><?php echo htmlspecialchars($form_address); ?></textarea>
              <label for="address">Alamat</label> -->
              <div class="select-wrapper">
                <select class="modern-select" id="vehicle" name="vehicle" required>
                  <option value="">-- Pilih Kendaraan --</option>
                  <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?= $vehicle['id'] ?>" <?= $form_vehicle_id === $vehicle['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($vehicle['id']) ?> | <?= htmlspecialchars($vehicle['model_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="select-wrapper">
                <select class="modern-select" id="purpose" name="type_order" required>
                  <option value="">-- Tentukan tujuan --</option>
                  <option value="test_driver" <?= $form_type_order === 'test_driver' ? 'selected' : '' ?>>Test Drive</option>
                  <option value="transaction" <?= $form_type_order === 'transaction' ? 'selected' : '' ?>>Transaksi</option>
                </select>
              </div>
              <div class="select-wrapper">
                <select class="modern-select" id="arrival_method" name="type_arrival" required>
                  <option value="">-- Tentukan Kedatangan --</option>
                  <option value="showroom" <?= $form_type_arrival === 'showroom' ? 'selected' : '' ?>>Saya akan datang ke showroom</option>
                  <option value="home_visit" <?= $form_type_arrival === 'home_visit' ? 'selected' : '' ?>>Saya ingin petugas datang ke lokasi saya</option>
                </select>
              </div>
            </div>
          </div>

          <div class="testDrive-container">
            <div id="flatpickr-modal"></div>
            <div class="datepicker-wrapper">
              <div class="datepicker-container">
                <input
                  class="modern-datepicker"
                  type="text"
                  id="date"
                  name="order_date"
                  required
                  placeholder="Pilih Jadwal"
                  value="<?= htmlspecialchars($form_order_date); ?>">

                <div class="datepicker-icon" id="datepickerIconTrigger">
                  <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" fill="currentColor" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="inputGroup">
              <input type="text" id="name" name="name" required autocomplete="off"
                value="">
              <label for="name">Kecamatan</label>
            </div>

            <div class="inputGroup">
              <input type="text" id="name" name="name" required autocomplete="off"
                value="">
              <label for="name">Desa/Kelurahan</label>
            </div>

            <div class="inputGroup">
              <input type="text" id="name" name="name" required autocomplete="off"
                value="">
              <label for="name">Jalan</label>
            </div>

            <div class="inputGroup">
              <input type="text" id="name" name="name" required autocomplete="off"
                value="">
              <label for="name">Kode Pos</label>
            </div>

            <button class="modern-location" type="button" onclick="getLocation()">Deteksi Lokasi Saya</button>
          </div>
      </div>
      <div class="form-actions">
        <div id="map"></div>
      </div>
      <div class="form-actions">
        <a href="index.php" class="btn-secondary">Kembali</a>
        <button type="submit" class="btn ">Lanjut</button>
      </div>
      </form>
    </section>
  </main>

  <?php include("./layouts/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <script src="./js/global.js"></script>
  <script src="./js/contact-us.js"></script>

</body>

</html>