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
    <section class="avm-vehicle-detail">
      <h2 id="detail-title">Honda Beat 2020 CBS ISS 110cc FI AT Merah • Green on Black</h2>

      <div class="avm-gallery">
        <div class="avm-main-image zoom-container">
          <img src="https://img.lacakharga.com/public/images/2024/06/honda-beat-fi-tahun-2012-1718341847.jpg" class="avm-thumbnail" alt="Honda Beat Front View" id="mainImage" data-index="1">
        </div>
        <div class="avm-thumbnails">
          <img src="./assets/images/detail-card/image 1.jpg" alt="Thumbnail 1" class="avm-thumbnail" data-index="2">
          <img src="./assets/images/detail-card/image 2.jpg" alt="Thumbnail 2" class="avm-thumbnail" data-index="3">
          <img src="./assets/images/detail-card/image 3.png" alt="Thumbnail 3" class="avm-thumbnail" data-index="4">
          <img src="./assets/images/detail-card/image 4.png" alt="Thumbnail 4" class="avm-thumbnail" data-index="5">
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
          <p>2020</p>
        </div>
        <div class="avm-spec-item"><strong>Kilometer</strong>
          <p>20.000</p>
        </div>
        <div class="avm-spec-item"><strong>Warna</strong>
          <p>Green on Black</p>
        </div>
        <div class="avm-spec-item"><strong>CC</strong>
          <p>110cc</p>
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
            <div class="harga color-orange" id="harga">Harga: Rp 14.500.000</div>
            <div class="lokasi">Lokasi: Cabang 1 – Jl. Gatot Subroto No.88, Bandung</div>
            <h3>Deskripsi</h3>
            <div>
              <p>Honda Beat 2020 CBS ISS 110cc FI AT – Green on Black<br>
                Honda Beat 2020 ini adalah pilihan terbaik bagi Anda yang mencari motor matic irit, gesit, dan nyaman untuk harian. Dengan teknologi PGM-FI (Fuel Injection), motor ini sangat hemat bahan bakar dan cocok untuk mobilitas di perkotaan.</p>
              <h3>Keunggulan Honda Beat 2020:</h3>
              <ul>
                <li><strong>Irit Bahan Bakar</strong> – Konsumsi PGM hingga 60 km/liter</li>
                <li><strong>Ringan & Gesit</strong> – Cocok untuk pemakaian di dalam kota</li>
                <li><strong>CBS & ISS</strong> – Sistem pengereman lebih aman dan fitur idling Stop System hemat bensin</li>
                <li><strong>Kondisi Mesin 100% Oke</strong> – Dijamin siap pakai tanpa kendala</li>
                <li><strong>Body Mulus</strong> – Tidak ada baret atau penyok</li>
                <li><strong>STNK Panjang</strong> – Berlaku sampai tahun 2025</li>
              </ul>
            </div>
          </div>

          <div class="avm-specs">
            <h3>Spesifikasi Teknis</h3>
            <table>
              <tr>
                <th>Tipe Mesin</th>
                <td>110cc, 4-langkah, SOHC, PGM-FI</td>
              </tr>
              <tr>
                <th>Daya Maksimum</th>
                <td>6.38 kW (8.7 PS) / 7.500 rpm</td>
              </tr>
              <tr>
                <th>Torsi Maksimum</th>
                <td>9.3 Nm / 5.500 rpm</td>
              </tr>
              <tr>
                <th>Sistem Transmisi</th>
                <td>Otomatis, V-Matic</td>
              </tr>
              <tr>
                <th>Sistem Pengereman</th>
                <td>CBS (Combi Brake System)</td>
              </tr>
              <tr>
                <th>Kapasitas Tangki</th>
                <td>4.2 liter</td>
              </tr>
              <tr>
                <th>Berat Kosong</th>
                <td>93 kg</td>
              </tr>
            </table>


            <div class="avm-action-buttons">
              <div class="avm-wrap-btn">
                <button type="button" class="avm-btn-primary" onclick="window.location='test-drive.php'">
                  <svg
                    width="30px"
                    height="30px"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g
                      id="SVGRepo_tracerCarrier"
                      stroke-linecap="round"
                      stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                      <path
                        d="M3 8L5.72187 10.2682C5.90158 10.418 6.12811 10.5 6.36205 10.5H17.6379C17.8719 10.5 18.0984 10.418 18.2781 10.2682L21 8M6.5 14H6.51M17.5 14H17.51M8.16065 4.5H15.8394C16.5571 4.5 17.2198 4.88457 17.5758 5.50772L20.473 10.5777C20.8183 11.1821 21 11.8661 21 12.5623V18.5C21 19.0523 20.5523 19.5 20 19.5H19C18.4477 19.5 18 19.0523 18 18.5V17.5H6V18.5C6 19.0523 5.55228 19.5 5 19.5H4C3.44772 19.5 3 19.0523 3 18.5V12.5623C3 11.8661 3.18166 11.1821 3.52703 10.5777L6.42416 5.50772C6.78024 4.88457 7.44293 4.5 8.16065 4.5ZM7 14C7 14.2761 6.77614 14.5 6.5 14.5C6.22386 14.5 6 14.2761 6 14C6 13.7239 6.22386 13.5 6.5 13.5C6.77614 13.5 7 13.7239 7 14ZM18 14C18 14.2761 17.7761 14.5 17.5 14.5C17.2239 14.5 17 14.2761 17 14C17 13.7239 17.2239 13.5 17.5 13.5C17.7761 13.5 18 13.7239 18 14Z"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </g>
                  </svg>
                  Tes Drive</button>
                <button type="button" class="avm-btn-primary" onclick="callUs()">
                  <svg width="25px" height="25px" viewBox="0 0 16.00 16.00" xmlns="http://www.w3.org/2000/svg" stroke-width="0.00016">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                      <path d="M11.42 9.49c-.19-.09-1.1-.54-1.27-.61s-.29-.09-.42.1-.48.6-.59.73-.21.14-.4 0a5.13 5.13 0 0 1-1.49-.92 5.25 5.25 0 0 1-1-1.29c-.11-.18 0-.28.08-.38s.18-.21.28-.32a1.39 1.39 0 0 0 .18-.31.38.38 0 0 0 0-.33c0-.09-.42-1-.58-1.37s-.3-.32-.41-.32h-.4a.72.72 0 0 0-.5.23 2.1 2.1 0 0 0-.65 1.55A3.59 3.59 0 0 0 5 8.2 8.32 8.32 0 0 0 8.19 11c.44.19.78.3 1.05.39a2.53 2.53 0 0 0 1.17.07 1.93 1.93 0 0 0 1.26-.88 1.67 1.67 0 0 0 .11-.88c-.05-.07-.17-.12-.36-.21z"></path>
                      <path d="M13.29 2.68A7.36 7.36 0 0 0 8 .5a7.44 7.44 0 0 0-6.41 11.15l-1 3.85 3.94-1a7.4 7.4 0 0 0 3.55.9H8a7.44 7.44 0 0 0 5.29-12.72zM8 14.12a6.12 6.12 0 0 1-3.15-.87l-.22-.13-2.34.61.62-2.28-.14-.23a6.18 6.18 0 0 1 9.6-7.65 6.12 6.12 0 0 1 1.81 4.37A6.19 6.19 0 0 1 8 14.12z"></path>
                    </g>
                  </svg>
                  Whatsapp</button>
              </div>
              <button type="button" class="avm-btn-secondary" onclick="saveToWishlist(this)" id="simpan">
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
        <form id="credit-form" class="credit-form">
          <div class="credit-form__container">

            <div class="credit-form__input-group">
              <input type="text" id="vehicle-name" class="credit-form__input" name="vehicle-name" required readonly autocomplete="off" value="Honda Beat">
              <label for="vehicle-name" class="credit-form__label">Nama Kendaraan</label>
            </div>

            <div class="credit-form__input-group">
              <input type="text" id="vehicle-price" class="credit-form__input" name="vehicle-price" required readonly autocomplete="off" value="Rp 10.000.000">
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
  </main>

  <!-- Footer -->
  <?php include("./layouts/footer.php"); ?>


  <script src="js/global.js"></script>
  <script src="js/detail.js"></script>
</body>

</html>