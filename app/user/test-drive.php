<!DOCTYPE html>
<html lang="id" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Drive - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS Choices -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <!-- JS Choices -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

</head>
<body>
    <!-- Navbar -->
    <?php include("./layouts/navbar.php");?>


    <main class="container">
        <section class="test-drive-form">
            <h2>Hubungi Kami untuk Test Drive</h2>
            <div>

            <form id="testDriveForm" class="testDriveForm">
            <div class="testDrive-container">

                <div class="inputGroup">
                    <input type="text" required="" autocomplete="off">
                    <label for="name">Name</label>
                </div>
                
                <div class="inputGroup">
                    <input type="tel" id="whatsapp" name="whatsapp" required autocomplete="off">
                    <label for="whatsapp">Nomor WhatsApp Aktif</label> 
                  </div>
                
                <div class="inputGroup">
                    <textarea id="address" name="address" rows="12" required></textarea>
                    <label for="address">Alamat</label>
                </div>
            </div>

            <div class="testDrive-container">
                <div class="inputGroup">
                  <input type="email" required="" autocomplete="off">
                  <label for="email">Email</label> 
                </div>
                
              <div class="select-wrapper">
              <select class="modern-select" id="vehicle" name="vehicle" required>
                <option value="">-- Pilih Kendaraan --</option>
                <option value="Honda Beat 2020">Honda Beat 2020</option>
                <option value="APV">APV</option>
                <option value="Baleno">Baleno</option>
                <option value="Hybrid Lux">Hybrid Lux</option>
                <option value="Honda Brio">Honda Brio</option>
                <option value="Mitsubishi Xpander">Mitsubishi Xpander</option>
              </select>
            </div>

                
                <div class="select-wrapper">
                  <select class="modern-select" id="purpose" name="purpose" required>
                    <option value="">-- Tentukan tujuan --</option>
                    <option value="Test Drive">Test Drive</option>
                    <option value="Order">Order</option>
                  </select>
                  <div class="select-arrow">
                    <svg viewBox="0 0 24 24" width="18" height="18">
                      <path d="M7 10l5 5 5-5z" fill="currentColor"/>
                    </svg>
                  </div>
                </div>
                
                <div class="datepicker-wrapper">
                  <label for="date">Tentukan Jadwal</label>
                  <div class="datepicker-container">
                    <input class="modern-datepicker" type="date" id="date" name="date" required>
                    <div class="datepicker-icon">
                      <svg viewBox="0 0 24 24" width="20" height="20">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" fill="currentColor"/>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

        </div>
                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">Kembali</a>
                    <button type="submit" class="btn">
                      Kirim
                   <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M20 4L3 9.31372L10.5 13.5M20 4L14.5 21L10.5 13.5M20 4L10.5 13.5" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                </div>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <?php include("./layouts/footer.php");?>


    <script src="js/global.js"></script>
    <script src="js/testDrive.js"></script>
</body>
</html>