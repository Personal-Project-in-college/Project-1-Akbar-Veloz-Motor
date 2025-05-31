<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Tambah Services</h3>

    <!-- Card Wrapper -->
    <div class="card">
      <div class="card-body">
        <form>
          <div class="mb-3">
            <label for="vehicle_code" class="form-label">Vehicle Code</label>
            <input type="text" class="form-control" id="vehicle_code" placeholder="Masukan Vehicle Code">
          </div>

          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <input type="number" class="form-control" id="status" placeholder="Masukan Status">
          </div>

          <div class="mb-3">
            <label for="ditangani" class="form-label">Ditangani Oleh</label>
            <input type="text" class="form-control" id="ditangani" placeholder="Masukan Di Tangani Oleh">
          </div>

          <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="text" class="form-control" id="harga" placeholder="Masukan Harga">
          </div>

          <div class="mb-3">
            <label for="status" class="form-label">Deskripsi</label>
            <input type="text" class="form-control" id="status" placeholder="Masukan Deskripsi">
          </div>

          <div class="mb-3">
            <label for="tannggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" placeholder="Masukan Tanggal">
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>

<?php include '../layout/footer.php'; ?>
