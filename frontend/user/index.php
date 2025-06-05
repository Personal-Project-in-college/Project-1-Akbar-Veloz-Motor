<!DOCTYPE html>
<html lang="id" translate="no">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Akbar Veloz Motor - Jual Beli Kendaraan Bekas</title>
  <link rel="stylesheet" href="css/style.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">

</head>

<body>
  <?php include("navbar.php"); ?>
  <main class="container">
    <!-- Carousel -->
    <section class="slideshow-container">
      <div class="banner-container">
        <div class="banner-slides">
          <div class="banner-slide">
            <img
              src="https://www.hondaibrm.co.id/assets/slider/72a3952588207e9c8fe5dac7d5c338e1.webp" />
          </div>
          <div class="banner-slide">
            <img
              src="https://www.hondaibrm.co.id/assets/slider/db4199b624c93e6bd687b0676892eb18.webp" />
          </div>
          <div class="banner-slide">
            <img
              src="https://www.hondaibrm.co.id/assets/slider/72a3952588207e9c8fe5dac7d5c338e1.webp" />
          </div>
        </div>

        <div class="banner-nav">
          <button class="banner-prev">❮</button>
          <button class="banner-next">❯</button>
        </div>

        <div class="banner-indicators">
          <div class="banner-indicator active" data-slide="0"></div>
          <div class="banner-indicator" data-slide="1"></div>
          <div class="banner-indicator" data-slide="2"></div>
        </div>
      </div>

      <!-- call us, test drive -->
      <div class="call-container" onclick="callUs()">
        <div class="call-card">
          <svg width="25px" height="25px" viewBox="0 0 16.00 16.00" xmlns="http://www.w3.org/2000/svg" stroke-width="0.00016">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path d="M11.42 9.49c-.19-.09-1.1-.54-1.27-.61s-.29-.09-.42.1-.48.6-.59.73-.21.14-.4 0a5.13 5.13 0 0 1-1.49-.92 5.25 5.25 0 0 1-1-1.29c-.11-.18 0-.28.08-.38s.18-.21.28-.32a1.39 1.39 0 0 0 .18-.31.38.38 0 0 0 0-.33c0-.09-.42-1-.58-1.37s-.3-.32-.41-.32h-.4a.72.72 0 0 0-.5.23 2.1 2.1 0 0 0-.65 1.55A3.59 3.59 0 0 0 5 8.2 8.32 8.32 0 0 0 8.19 11c.44.19.78.3 1.05.39a2.53 2.53 0 0 0 1.17.07 1.93 1.93 0 0 0 1.26-.88 1.67 1.67 0 0 0 .11-.88c-.05-.07-.17-.12-.36-.21z"></path>
              <path d="M13.29 2.68A7.36 7.36 0 0 0 8 .5a7.44 7.44 0 0 0-6.41 11.15l-1 3.85 3.94-1a7.4 7.4 0 0 0 3.55.9H8a7.44 7.44 0 0 0 5.29-12.72zM8 14.12a6.12 6.12 0 0 1-3.15-.87l-.22-.13-2.34.61.62-2.28-.14-.23a6.18 6.18 0 0 1 9.6-7.65 6.12 6.12 0 0 1 1.81 4.37A6.19 6.19 0 0 1 8 14.12z"></path>
            </g>
          </svg>
          <h5 style="margin-top: 5px">Whatsapp</h5>
        </div>

        <div class="call-card" onclick="window.location='test-drive.php'">
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
          <h5>Test Drive</h5>
        </div>
      </div>
    </section>

    <section class="vehicle-list">
      <h2>Daftar Kendaraan</h2>
      <div class="tab">
        <button class="tablinks active" onclick="openTab(event, 'Semua')">Semua</button>
        <button class="tablinks" onclick="openTab(event, 'Motor')">Motor</button>
        <button class="tablinks" onclick="openTab(event, 'Mobil')">Mobil</button>
      </div>

      <!-- Semua -->
      <div id="Semua" class="tabcontent" style="display: block;">
        <div class="grid-container" id="semua-container">

        </div>
      </div>

      <!-- Motor -->
      <div id="Motor" class="tabcontent">
        <div class="grid-container" id="motor-container"></div>
      </div>

      <!-- Mobil -->
      <div id="Mobil" class="tabcontent">
        <div class="grid-container" id="mobil-container"></div>
      </div>
    </section>

    </section>

    <section class="location">
      <h2>Lokasi Showroom</h2>
      <div class="location-section">
        <div class="map-wrapper">
          <div class="map-container">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31709.467082367086!2d107.76811926910234!3d-6.561590679615411!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e693b9ae39cc0eb%3A0x76db7b3959df2011!2sPoliteknik%20Negeri%20Subang%2C%20Kampus%20Utama%20Cibogo!5e0!3m2!1sid!2sid!4v1739072152156!5m2!1sid!2sid"
              class="map-frame"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>

        <div class="location-info">
          <p>
            Jl. Brigjen Katamso No.37, Dangdeur, Kec. Subang, Kabupaten
            Subang, Jawa Barat 41213
          </p>
          <p><strong>Jam Operasional:</strong> Senin-Sabtu, 08:00-17:00</p>
          <p><strong>Telepon:</strong> (0260) 411015</p>
        </div>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <?php include("footer.php"); ?>

  <script src="js/global.js"></script>
  <script src="js/script.js"></script>
</body>

</html>