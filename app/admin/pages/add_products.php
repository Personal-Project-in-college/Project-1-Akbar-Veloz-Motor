<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Tambah Products</h3>
      <div class="container mt-4">
        <div class="card">
          <div class="card-body">
            <form>
              <!-- Vehicle Name & Category -->
              <div class="mb-3">
                <label for="vehicleName" class="form-label">Nama Kendaraan</label>
                <input type="text" class="form-control" id="NamaKendaraan" placeholder="e.g. Vario">
              </div>

              <div class="mb-3">
                <label for="vehicleCategory" class="form-label">Kategori Kendaraan</label>
                <select class="form-select" id="vehicleCategory">
                  <option selected>Pilih</option>
                  <option>Motor</option>
                  <option>Mobil</option>
                </select>
              </div>

              <!-- Vehicle Detail -->
              <div class="row">
                <label class="form-label mb-2">Detail Kendaraan</label>
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" placeholder="Warna, dll">
                </div>
                <div class="col-md-4 mb-3">
                  <select class="form-select">
                    <option selected>Pilih Tahun</option>
                    <option>2025</option>
                    <option>2024</option>
                    <option>2023</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" placeholder="Input Serial">
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" placeholder="Input Harga">
                </div>
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" placeholder="Input Kilometer">
                </div>
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" placeholder="Input CC">
                </div>
              </div>

              <!-- Status & Description -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <select class="form-select">
                    <option selected>Status</option>
                    <option>Tersedia</option>
                    <option>Terjual</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <select class="form-select">
                    <option selected>Deskripsi</option>
                    <option>Bekas</option>
                  </select>
                </div>
              </div>

              <!-- Stock & Insurance -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <select class="form-select">
                    <option selected>Stock</option>
                    <option>Tersedia</option>
                    <option>Proses Pemesanan</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <input type="date" class="form-control" placeholder="Insurance Date">
                </div>
              </div>

              <!-- Picture Upload -->
              <div class="mb-3">
                <label class="form-label">Gambar</label>
                <input type="file" class="form-control">
              </div>

              <button type="submit" class="btn btn-primary mt-3">Simpan</button>

              </div>
            </form>
          </div>
        </div>
      </div>

<?php include '../layout/footer.php'; ?>
