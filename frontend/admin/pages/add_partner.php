<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Tambah Partner</h3>

    <!-- Card Wrapper -->
    <div class="card">
      <div class="card-body">
        <form>
          <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" placeholder="Masukan Nama">
          </div>

          <div class="mb-3">
            <label class="form-label">Foto</label>
            <input type="file" class="form-control">
          </div>
          
          <div class="mb-3">
            <label class="form-label">Foto KTP4</label>
            <input type="file" class="form-control">
          </div>

          <div class="mb-3">
            <label for="no ponsel" class="form-label">No Ponsel</label>
            <input type="no ponsel" class="form-control" id="no ponsel" placeholder="Masukan No Ponsel">
          </div>

          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="alamat" class="form-control" id="alamat" placeholder="Masukan Alamat">
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>

<?php include '../layout/footer.php'; ?>
