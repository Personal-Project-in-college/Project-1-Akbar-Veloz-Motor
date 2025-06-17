<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include_once './config/koneksi.php';
$vehicle_id = $_GET['id'] ?? null;
if (!$vehicle_id) {
  die("Error: ID Kendaraan tidak ditemukan.");
}
$vehicle = null;
$photos = [];
$db_error = null;

try {
  $stmt = $koneksi->prepare("
        SELECT
            v.*,
            vm.name AS model_name,
            b.name AS brand_name,
            br.name AS branch_name,
            br.address AS branch_address
        FROM
            vehicles v
        JOIN
            vehicle_models vm ON v.vehicle_model_id = vm.id
        JOIN
            brands b ON vm.brand_id = b.id
        JOIN
            branches br ON v.branch_id = br.id
        WHERE
            v.id = :vehicle_id AND v.deleted_at IS NULL
    ");
  $stmt->execute([':vehicle_id' => $vehicle_id]);
  $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$vehicle) {
    die("Kendaraan dengan ID '$vehicle_id' tidak ditemukan.");
  }

  $stmt_photos = $koneksi->prepare("
        SELECT photo_path, is_cover
        FROM vehicle_photos
        WHERE vehicle_id = :vehicle_id AND deleted_at IS NULL
        ORDER BY is_cover DESC, id ASC
    ");
  $stmt_photos->execute([':vehicle_id' => $vehicle_id]);
  $photos = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log('Database Error on Detail Page: ' . $e->getMessage());
  $db_error = "Gagal memuat data detail kendaraan. Silakan coba lagi nanti.";
}

$display_name = htmlspecialchars($vehicle['brand_name'] . ' ' . $vehicle['model_name'] . ' ' . date('Y', strtotime($vehicle['production_year'])));
$formatted_price = 'Rp ' . number_format($vehicle['price_displayed'], 0, ',', '.');
$base_image_url = './storage/';

?>

<!DOCTYPE html>
<html lang="id" translate="no">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Kendaraan - Akbar Veloz Motor</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
  <!-- Navbar -->
  <?php include("./layouts/navbar.php"); ?>


  <main class="container">
    <?php if ($db_error): ?>
      <p style="text-align: center; color: red;"><?php echo $db_error; ?></p>
    <?php elseif ($vehicle): ?>
      <section class="avm-vehicle-detail">
        <h2 id="detail-title"><?php echo $display_name . ' ' . htmlspecialchars($vehicle['color']); ?></h2>

        <div class="avm-gallery">
          <?php
          $main_image = './assets/images/placeholder.png';
          if (!empty($photos)) {
            $main_image_path = $base_image_url . $photos[0]['photo_path'];
            if (file_exists($main_image_path)) {
              $main_image = $main_image_path;
            }
            array_shift($photos);
          }
          ?>
          <div class="avm-main-image zoom-container">
            <img src="<?php echo $main_image; ?>" alt="<?php echo $display_name; ?> - Gambar Utama" id="mainImage" data-index="0">
          </div>
          <div class="avm-thumbnails">
            <?php if (!empty($photos)): ?>
              <?php foreach ($photos as $index => $photo): ?>
                <?php
                $thumbnail_path = $base_image_url . $photo['photo_path'];
                if (file_exists($thumbnail_path)):
                ?>
                  <img src="<?php echo $thumbnail_path; ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="avm-thumbnail" data-index="<?php echo $index + 1; ?>">
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Image Modal -->
        <div id="imageModal" class="avm-modal">
          <span class="avm-close-modal">&times;</span>
          <div class="avm-modal-content">
            <img id="modalImage" src="" alt="" class="modal-image-container">
            <div class="avm-modal-nav">
              <button class="avm-modal-prev"><i class="fas fa-chevron-left"></i></button>
              <button class="avm-modal-next"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="avm-modal-zoom">
              <button class="avm-zoom-in"><i class="fas fa-search-plus"></i></button>
              <button class="avm-zoom-out"><i class="fas fa-search-minus"></i></button>
              <button class="avm-reset-zoom"><i class="fas fa-sync-alt"></i></button>
            </div>
          </div>
        </div>

        <div class="avm-specs-grid">
          <div class="avm-spec-item"><strong>Tahun kendaraan</strong>
            <p><?php echo date('Y', strtotime($vehicle['production_year'])); ?></p>
          </div>
          <div class="avm-spec-item"><strong>Kilometer</strong>
            <p><?php echo number_format($vehicle['kilometer']); ?> Km</p>
          </div>
          <div class="avm-spec-item"><strong>Warna</strong>
            <p><?php echo htmlspecialchars($vehicle['color']); ?></p>
          </div>
          <div class="avm-spec-item"><strong>CC</strong>
            <p><?php echo htmlspecialchars($vehicle['cc_engine']); ?>cc</p>
          </div>
        </div>

        <div class="tab tab-detail">
          <button class="tablinks active" onclick="openTab(event, 'Deskripsi')">Deskripsi</button>
          <button class="tablinks" onclick="openTab(event, 'Kredit')">Kredit</button>
        </div>

        <!-- Deskripsi -->
        <div id="Deskripsi" class="tabcontent" style="display: block;">
          <div class="avm-detail-content font-Poppins">
            <div class="avm-description">
              <div class="harga color-orange" id="harga">Harga: <?php echo $formatted_price; ?></div>
              <div class="lokasi">Lokasi: <?php echo htmlspecialchars($vehicle['branch_name']); ?></div>
              <h3>Deskripsi</h3>
              <div>
                <p><?php echo nl2br(htmlspecialchars($vehicle['description'])); ?></p>
              </div>
            </div>

            <div class="avm-specs">
              <h3>Spesifikasi Teknis</h3>
              <table>
                <tr>
                  <th>Tipe Mesin</th>
                  <td><?php echo htmlspecialchars($vehicle['cc_engine']); ?>cc, 4-Langkah, SOHC, <?php echo htmlspecialchars(ucfirst($vehicle['type_fuel'])); ?></td>
                </tr>
                <tr>
                  <th>Tahun</th>
                  <td><?php echo date('Y', strtotime($vehicle['production_year'])); ?></td>
                </tr>
                <tr>
                  <th>Kilometer</th>
                  <td><?php echo number_format($vehicle['kilometer']); ?> Km</td>
                </tr>
                <tr>
                  <th>Warna</th>
                  <td><?php echo htmlspecialchars($vehicle['color']); ?></td>
                </tr>
                <tr>
                  <th>Masa Berlaku STNK</th>
                  <td><?php echo date('d F Y', strtotime($vehicle['stnk_deadline'])); ?></td>
                </tr>
              </table>


              <div class="avm-action-buttons">
                <div class="avm-wrap-btn">
                  <button type="button" class="avm-btn-primary" onclick="window.location='contact-us.php?purpose=test_drive'">
                    <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                      <g id="SVGRepo_iconCarrier">
                        <path d="M3 8L5.72187 10.2682C5.90158 10.418 6.12811 10.5 6.36205 10.5H17.6379C17.8719 10.5 18.0984 10.418 18.2781 10.2682L21 8M6.5 14H6.51M17.5 14H17.51M8.16065 4.5H15.8394C16.5571 4.5 17.2198 4.88457 17.5758 5.50772L20.473 10.5777C20.8183 11.1821 21 11.8661 21 12.5623V18.5C21 19.0523 20.5523 19.5 20 19.5H19C18.4477 19.5 18 19.0523 18 18.5V17.5H6V18.5C6 19.0523 5.55228 19.5 5 19.5H4C3.44772 19.5 3 19.0523 3 18.5V12.5623C3 11.8661 3.18166 11.1821 3.52703 10.5777L6.42416 5.50772C6.78024 4.88457 7.44293 4.5 8.16065 4.5ZM7 14C7 14.2761 6.77614 14.5 6.5 14.5C6.22386 14.5 6 14.2761 6 14C6 13.7239 6.22386 13.5 6.5 13.5C6.77614 13.5 7 13.7239 7 14ZM18 14C18 14.2761 17.7761 14.5 17.5 14.5C17.2239 14.5 17 14.2761 17 14C17 13.7239 17.2239 13.5 17.5 13.5C17.7761 13.5 18 13.7239 18 14Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                      </g>
                    </svg>
                    Tes Drive
                  </button>
                  <button type="button" class="avm-btn-primary" onclick="whatsapp()">
                    <svg width="25px" height="25px" viewBox="0 0 16.00 16.00" xmlns="http://www.w3.org/2000/svg" stroke-width="0.00016">
                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                      <g id="SVGRepo_iconCarrier">
                        <path d="M11.42 9.49c-.19-.09-1.1-.54-1.27-.61s-.29-.09-.42.1-.48.6-.59.73-.21.14-.4 0a5.13 5.13 0 0 1-1.49-.92 5.25 5.25 0 0 1-1-1.29c-.11-.18 0-.28.08-.38s.18-.21.28-.32a1.39 1.39 0 0 0 .18-.31.38.38 0 0 0 0-.33c0-.09-.42-1-.58-1.37s-.3-.32-.41-.32h-.4a.72.72 0 0 0-.5.23 2.1 2.1 0 0 0-.65 1.55A3.59 3.59 0 0 0 5 8.2 8.32 8.32 0 0 0 8.19 11c.44.19.78.3 1.05.39a2.53 2.53 0 0 0 1.17.07 1.93 1.93 0 0 0 1.26-.88 1.67 1.67 0 0 0 .11-.88c-.05-.07-.17-.12-.36-.21z"></path>
                        <path d="M13.29 2.68A7.36 7.36 0 0 0 8 .5a7.44 7.44 0 0 0-6.41 11.15l-1 3.85 3.94-1a7.4 7.4 0 0 0 3.55.9H8a7.44 7.44 0 0 0 5.29-12.72zM8 14.12a6.12 6.12 0 0 1-3.15-.87l-.22-.13-2.34.61.62-2.28-.14-.23a6.18 6.18 0 0 1 9.6-7.65 6.12 6.12 0 0 1 1.81 4.37A6.19 6.19 0 0 1 8 14.12z"></path>
                      </g>
                    </svg>
                    Whatsapp
                  </button>
                </div>
                <button type="button" class="avm-btn-secondary" onclick="saveToWishlist(this)" id="simpan"
                  data-id="<?php echo htmlspecialchars($vehicle['id']); ?>"
                  data-name="<?php echo $display_name; ?>"
                  data-price="<?php echo $formatted_price; ?>"
                  data-image="<?php echo $main_image; ?>"
                  data-detail-url="<?php echo 'detail.php?id=' . htmlspecialchars($vehicle['id']); ?>">
                  <svg width="35px" height="35px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </svg>
                  Simpan
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Kredit Section -->
        <div id="Kredit" class="tabcontent">
          <p class="credit-disclaimer">
            Catatan: Simulasi ini bersifat ilustratif. Suku bunga rata-rata cicilan kendaraan di Indonesia biasanya berkisar antara 3% hingga 10% per tahun, tergantung pada jenis kendaraan, durasi cicilan, uang muka, dan kebijakan lembaga leasing. Untuk skema kredit leasing yang akurat di Akbar Veloz Motor, silakan hubungi tim penjualan kami secara langsung. <a href="laporan_leasing_kendaraan.php" class="laporan-komprehensif">data rata-rata bunga</a>
          </p>
          <form id="credit-form" class="credit-form">
            <div class="credit-form__container">

              <div class="credit-form__input-group">
                <input type="text" id="vehicle-name" class="credit-form__input" name="vehicle-name" required readonly autocomplete="off" value="<?php echo $display_name; ?>">
                <label for="vehicle-name" class="credit-form__label">Nama Kendaraan</label>
              </div>

              <div class="credit-form__input-group">
                <input type="text" id="vehicle-price" class="credit-form__input" name="vehicle-price" required readonly autocomplete="off" value="<?php echo 'Rp ' . number_format($vehicle['price_displayed'], 0, ',', '.'); ?>">
                <label for="vehicle-price" class="credit-form__label">Harga</label>
              </div>

              <div class="credit-form__installment-options">
                <label class="credit-form__option-label">Pilih durasi cicilan per bulan (minimal uang muka 25%)</label>
                <div class="credit-form__button-group">
                  <button type="button" class="credit-form__period-button">2</button>
                  <button type="button" class="credit-form__period-button">6</button>
                  <button type="button" class="credit-form__period-button">10</button>
                  <button type="button" class="credit-form__period-button">12</button>
                  <button type="button" class="credit-form__period-button">24</button>
                  <button type="button" class="credit-form__period-button">36</button>
                  <button type="button" class="credit-form__period-button">48</button>
                  <button type="button" class="credit-form__period-button">60</button>
                </div>
              </div>

              <div class="credit-form__input-group">
                <input type="text" id="down-payment" class="credit-form__input" name="down-payment" required autocomplete="off" value="Rp 0">
                <label for="down-payment" class="credit-form__label">DP</label>
              </div>

              <div class="credit-form__input-group credit-form__input-group--with-button">
                <input type="text" id="interest-rate" class="credit-form__input" name="interest-rate" required autocomplete="off" value="0">
                <label for="interest-rate" class="credit-form__label">Bunga</label>
                <button type="button" class="credit-form__action-button">
                  Calculate
                </button>
              </div>

              <div class="credit-form__summary">
                <p class="credit-form__summary-text">Total harga yang harus dibayar per bulan:</p>
                <div class="credit-form__payment-amount">
                  <span class="credit-form__payment-value">Rp. 0</span>
                  <span class="credit-form__payment-period">/bulan</span>
                </div>
              </div>
            </div>
          </form>
        </div>
      </section>
    <?php endif; ?>

  </main>

  <!-- Footer -->
  <?php include("./layouts/footer.php"); ?>


  <script src="js/global.js"></script>
  <script src="js/detail.js"></script>
</body>

</html>