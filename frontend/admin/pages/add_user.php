<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Tambah User</h3>

    <!-- Card Wrapper -->
    <div class="card">
      <div class="card-body">
        <form>
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" placeholder="Masukan username">
          </div>

          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role">
              <option selected>Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="staff">Staff</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Masukan Email">
          </div>

          <div class="mb-3">
            <label for="phone" class="form-label">Nomor Ponsel</label>
            <input type="tel" class="form-control" id="phone" placeholder="Masukan Nomor Ponsel">
          </div>

          <div class="mb-3">
            <label for="tempat" class="form-label">Tempat Bekerja</label>
            <select class="form-select" id="tempat">
              <option selected>Pilih Tempat Kerja</option>
              <option value="cabang1">Showroom Utama</option>
              <option value="cabang2">Cabang</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Masukan Password">
          </div>

          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="confirmPassword" placeholder="Masukan Konfirmasi Password">
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>

<?php include '../layout/footer.php'; ?>
