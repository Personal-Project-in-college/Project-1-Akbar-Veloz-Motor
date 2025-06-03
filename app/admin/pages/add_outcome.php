<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Tambah Outcome</h3>

<!-- Card Wrapper -->
<div class="card">
  <div class="card-body">
    <form action="simpan_outcome.php" method="POST">
      <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" required>
      </div>

      <div class="mb-3">
        <label for="jenis" class="form-label">Jenis Pengeluaran</label>
        <input type="text" class="form-control" id="jenis" name="jenis_pengeluaran" placeholder="Contoh: Service, Listrik, dll." required>
      </div>

      <div class="mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Deskripsi singkat pengeluaran" required>
      </div>

      <div class="mb-3">
        <label for="nominal" class="form-label">Nominal</label>
        <input type="number" class="form-control" id="nominal" name="nominal" placeholder="Contoh: 150000" required>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
  </div>
</div>


<?php include '../layout/footer.php'; ?>
