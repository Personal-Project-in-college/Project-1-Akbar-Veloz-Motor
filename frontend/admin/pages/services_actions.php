<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Detail Order</h3>
    <div class="container mt-4">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-4">Aksi Layanan</h4>
      <form>
        <div class="row">
          <!-- Kolom Kiri -->
          <div class="col-md-6">
            <div class="mb-3">
              <label for="kodeKendaraan" class="form-label">Kode Kendaraan</label>
              <input type="text" class="form-control" id="kodeKendaraan" placeholder="Input Vehicle Code">
            </div>
            <div class="mb-3">
              <label for="ubahStatus" class="form-label">Ubah Status</label>
              <select class="form-select" id="ubahStatus">
                <option selected>Pilih</option>
                <option value="tersedia">Tersedia</option>
                <option value="dijual">Dijual</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">
            <h6>Detail Kendaraan</h6>
            <div class="row">
              <div class="col-md-6 mb-2">
                <input type="text" class="form-control" placeholder="Nama Kendaraan">
              </div>
              <div class="col-md-6 mb-2">
                <input type="text" class="form-control" placeholder="No. Polisi">
              </div>
              <div class="col-md-6 mb-2">
                <input type="text" class="form-control" placeholder="Warna">
              </div>
              <div class="col-md-6 mb-2">
                <input type="text" class="form-control" placeholder="Mesin">
              </div>
              <div class="col-md-6 mb-2">
                <input type="text" class="form-control" placeholder="Kilometer">
              </div>
              <div class="col-md-6 mb-2">
                <select class="form-select">
                  <option selected>Pilih Tahun</option>
                  <option>2025</option>
                  <option>2024</option>
                </select>
              </div>
              <div class="col-12 mb-2">
                <input type="text" class="form-control" placeholder="Status Kendaraan">
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include '../layout/footer.php'; ?>