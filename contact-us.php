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
$selected_order_type = $_GET['type_order'] ?? ''; // type_order dari URL

$negotiated_price_from_chat = null;
if (!empty($selected_vehicle) && isset($_SESSION['negotiated_price_' . $selected_vehicle])) {
    $negotiated_price_from_chat = $_SESSION['negotiated_price_' . $selected_vehicle];
    // Opsional: Hapus dari sesi setelah digunakan jika hanya perlu sekali pakai
    // unset($_SESSION['negotiated_price_' . $selected_vehicle]);
    // unset($_SESSION['last_negotiated_vehicle_id']);
}

$customer_id = $_SESSION['customer_id'];
$customer_name = '';
$customer_email = '';
$customer_phone = '';
$customer_address = '';
$customer_latitude = null;
$customer_longitude = null;

$cekPesanan = $koneksi->prepare("SELECT id FROM orders WHERE customer_id = ? AND status = 'proced' LIMIT 1");
$cekPesanan->execute([$customer_id]);
$pesananAktif = $cekPesanan->fetch(PDO::FETCH_ASSOC);

if ($pesananAktif) {
  header("Location: detail-pesanan.php?id=" . $pesananAktif['id']);
  exit();
}

try {
  $stmt = $koneksi->prepare("SELECT name, email, phone, address, latitude, longitude FROM customers WHERE id = ?");
  $stmt->execute([$customer_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $customer_name = $user['name'];
    $customer_email = $user['email'];
    $customer_phone = $user['phone'];
    $customer_address = $user['address'];
    $customer_latitude = $user['latitude'];
    $customer_longitude = $user['longitude'];
  }
} catch (PDOException $e) {
  error_log("Error saat mengambil data pengguna: " . $e->getMessage());
  header("Location: error.php?msg=" . urlencode("Terjadi kesalahan saat memuat data Anda."));
  exit();
}

// Inisialisasi atau update $_SESSION['pending_order']
// Pastikan $selected_order_type dari URL memiliki prioritas saat pertama kali datang,
// atau jika sesi pending_order sudah ada, update jika ada perubahan di URL.
if (!isset($_SESSION['pending_order'])) {
  $_SESSION['pending_order'] = [
    'name' => $customer_name,
    'email' => $customer_email,
    'phone' => $customer_phone,
    'address' => $customer_address,
    'vehicle_id' => $selected_vehicle,
    'type_order' => $selected_order_type, // Ambil dari URL saat inisialisasi
    'type_arrival' => '',
    'order_date' => '',
    'negotiated_price' => $negotiated_price_from_chat,
    'latitude' => $customer_latitude,
    'longitude' => $customer_longitude
  ];
} else {
  // Update nilai jika ada di URL, tapi jaga nilai yang sudah ada jika tidak ada di URL
  $_SESSION['pending_order']['name'] = $_SESSION['pending_order']['name'] ?? $customer_name;
  $_SESSION['pending_order']['email'] = $_SESSION['pending_order']['email'] ?? $customer_email;
  $_SESSION['pending_order']['phone'] = $_SESSION['pending_order']['phone'] ?? $customer_phone;
  $_SESSION['pending_order']['address'] = $_SESSION['pending_order']['address'] ?? $customer_address;
  $_SESSION['pending_order']['vehicle_id'] = $selected_vehicle; // Selalu update dari URL
  $_SESSION['pending_order']['type_order'] = $selected_order_type; // Selalu update dari URL
  $_SESSION['pending_order']['negotiated_price'] = $_SESSION['pending_order']['negotiated_price'] ?? $negotiated_price_from_chat;
  $_SESSION['pending_order']['latitude'] = $_SESSION['pending_order']['latitude'] ?? $customer_latitude;
  $_SESSION['pending_order']['longitude'] = $_SESSION['pending_order']['longitude'] ?? $customer_longitude;
}


$alert_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $required_fields = ['name', 'email', 'phone', 'vehicle', 'type_order', 'type_arrival', 'order_date', 'address'];
  $missing_fields = [];

  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      $missing_fields[] = $field;
    }
  }

  $full_address = trim($_POST['address'] ?? '');
  $latitude = $_POST['latitude'] ?? null;
  $longitude = $_POST['longitude'] ?? null;

  if (!empty($missing_fields)) {
    $field_names = [
      'name' => 'Nama',
      'email' => 'Email',
      'phone' => 'Nomor WhatsApp Aktif',
      'vehicle' => 'Kendaraan',
      'type_order' => 'Tujuan',
      'type_arrival' => 'Kedatangan',
      'order_date' => 'Jadwal',
      'address' => 'Alamat Lengkap'
    ];
    $missing_field_names = array_map(function ($field) use ($field_names) {
      return $field_names[$field] ?? $field;
    }, $missing_fields);
    $alert_message = "Harap isi semua kolom yang wajib diisi: " . implode(', ', $missing_field_names) . ".";
  } else {
    // Saat form POST, nilai dari POST yang seharusnya disimpan ke session
    $_SESSION['pending_order'] = [
      'name' => $_POST['name'],
      'email' => $_POST['email'],
      'phone' => $_POST['phone'],
      'address' => $full_address,
      'vehicle_id' => $_POST['vehicle'],
      'type_order' => $_POST['type_order'], // Ambil dari POST
      'type_arrival' => $_POST['type_arrival'],
      'order_date' => $_POST['order_date'],
      'negotiated_price' => $_SESSION['pending_order']['negotiated_price'] ?? null,
      'latitude' => $latitude,
      'longitude' => $longitude
    ];

    header("Location: preview-detail-pesanan.php?preview=true");
    exit();
  }
}

$vehiclesQuery = $koneksi->query("SELECT v.id, vm.name AS model_name FROM vehicles AS v JOIN vehicle_models vm ON v.vehicle_model_id = vm.id WHERE v.status = 'available' AND v.deleted_at IS NULL");
$vehicles = $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC);

// Gunakan nilai dari session terlebih dahulu untuk mengisi form
$form_name = $_SESSION['pending_order']['name'] ?? $customer_name;
$form_email = $_SESSION['pending_order']['email'] ?? $customer_email;
$form_phone = $_SESSION['pending_order']['phone'] ?? $customer_phone;
$form_full_address = $_SESSION['pending_order']['address'] ?? $customer_address;
$form_vehicle_id = $_SESSION['pending_order']['vehicle_id'] ?? ''; // Pastikan string kosong jika tidak ada
$form_type_order = $_SESSION['pending_order']['type_order'] ?? ''; // Pastikan string kosong jika tidak ada
$form_type_arrival = $_SESSION['pending_order']['type_arrival'] ?? '';

$form_latitude = $_SESSION['pending_order']['latitude'] ?? '';
$form_longitude = $_SESSION['pending_order']['longitude'] ?? '';

$form_order_date = $_SESSION['pending_order']['order_date'] ?? ($_GET['order_date'] ?? '');

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
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


        #modal-map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .avm-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.7);
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .avm-modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            max-height: max-content;
            max-width: 700px;
            width: 90%;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .avm-modal-close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .avm-modal-close:hover,
        .avm-modal-close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-coords-display {
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #555;
        }

        .modal-coords-display span {
            font-weight: bold;
            color: #333;
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 15px;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php include("./layouts/navbar.php"); ?>

    <main class="container">
        <section class="test-drive-form">
            <h2>Hubungi Kami</h2>
            <div>
                <?php if (!empty($alert_message ?? '')): ?>
                    <div class="alert-message" style="background-color: #ffdddd; color: #d8000c; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <?= htmlspecialchars($alert_message ?? '') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="testDriveForm" class="testDriveForm" novalidate>
                    <div class="wrap-input-test-drive">
                        <div class="testDrive-container">
                            <div class="inputGroup">
                                <input type="text" id="name" name="name" required autocomplete="off"
                                    value="<?php echo htmlspecialchars($form_name ?? ''); ?>">
                                <label for="name">Name</label>
                            </div>

                            <div class="inputGroup">
                                <input type="tel" id="whatsapp" name="phone" required autocomplete="off"
                                    value="<?php echo htmlspecialchars($form_phone ?? ''); ?>">
                                <label for="whatsapp">Nomor WhatsApp Aktif</label>
                            </div>
                            <div class="inputGroup">
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
                                    <select class="modern-select" id="purpose" name="type_order" required >
                                        <option value="">-- Tentukan tujuan --</option>
                                        <option value="test_driver" <?= $form_type_order === 'test_driver' ? 'selected' : '' ?>>Test Drive</option>
                                        <option value="transaction" <?= $form_type_order === 'transaction' ? 'selected' : '' ?>>Transaksi</option>
                                    </select>
                                </div>
                                <div class="select-wrapper">
                                    <select class="modern-select" id="arrival_method" name="type_arrival" required >
                                        <option value="">-- Tentukan Kedatangan --</option>
                                        <option value="showroom" <?= $form_type_arrival === 'showroom' ? 'selected' : '' ?>>Saya akan datang ke showroom</option>
                                        <option value="home_visit" <?= $form_type_arrival === 'home_visit' ? 'selected' : '' ?>>Saya ingin petugas datang ke lokasi saya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="testDrive-container">
                            <div class="inputGroup">
                                <input type="email" id="email" name="email" required autocomplete="off" readonly
                                    value="<?php echo htmlspecialchars($form_email ?? ''); ?>">
                                <label for="email">Email</label>
                            </div>

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
                                        value="<?= htmlspecialchars($form_order_date ?? ''); ?>">

                                    <div class="datepicker-icon" id="datepickerIconTrigger">
                                        <svg viewBox="0 0 24 24" width="20" height="20">
                                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" fill="currentColor" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="inputGroup">
                                <textarea id="address" name="address" rows="7" required autocomplete="off"><?= htmlspecialchars($form_full_address ?? ''); ?></textarea>
                                <label for="address">Alamat Lengkap</label>
                            </div>

                            <input type="hidden" id="latitude" name="latitude" value="<?= htmlspecialchars($form_latitude ?? ''); ?>">
                            <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($form_longitude ?? ''); ?>">

                            <button class="modern-location" type="button" onclick="openLocationModal()">Atur Pinpoint Lokasi</button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn-secondary">Kembali</a>
                        <button type="submit" class="btn ">Lanjut</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include("./layouts/footer.php"); ?>

    <div id="imageModal" class="avm-modal">
        <div class="avm-modal-content">
            <span class="avm-modal-close" onclick="closeLocationModal()">&times;</span>
            <h2>Pilih Lokasi Anda</h2>
            <div id="modal-map"></div>
            <div class="modal-coords-display">
                Latitude: <span id="modal-latitude-display"></span>, Longitude: <span id="modal-longitude-display"></span>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeLocationModal()">Batal</button>
                <button type="button" class="btn" onclick="saveLocationAndCloseModal()">Pilih Lokasi Ini</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="./js/global.js"></script>
    <script src="./js/contact-us.js"></script>

</body>

</html>