<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Settings</h3>
<div class="container mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
    <div class="list-group">
        <a href="#" class="list-group-item list-group-item-action list-group-item-secondary active">Edit Profil</a>
        <a href="#" class="list-group-item list-group-item-action">Akun</a>
    </div>
    </div>

    <!-- Konten -->
    <div class="col-md-9">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Foto Profil</h5>
          <div class="d-flex align-items-center gap-3">
            <img src="https://via.placeholder.com/60" class="rounded-circle" alt="Avatar">
            <div class="mb-3">
            <label for="profilePhoto" class="form-label">Foto Profil</label>
            <input class="form-control" type="file" id="profilePhoto" name="profilePhoto">
            </div>
            <button class="btn btn-outline-danger">Hapus</button>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Detail Pengguna</h5>
          <form>
            <div class="mb-2">
              <label class="form-label">Nama Depan</label>
              <input type="text" class="form-control" placeholder="......">
            </div>
            <div class="mb-2">
              <label class="form-label">Nama Belakang</label>
              <input type="text" class="form-control" placeholder="......">
            </div>
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" placeholder="......">
            </div>
            <div class="mb-2">
              <label class="form-label">No. Telepon</label>
              <input type="text" class="form-control" placeholder="......">
            </div>
            <button class="btn btn-primary mt-2">Simpan</button>
          </form>
        </div>
      </div>

      <div class="alert alert-success d-flex justify-content-between align-items-center">
        <span>Berhasil disimpan! Pengaturan profil telah diperbarui.</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
