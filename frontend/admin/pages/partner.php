<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">Partner</h3>

       <!-- Actions -->
      <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
        <!-- Tambahkan Produk Button -->
        <a href="tambah_produk.php" class="btn btn-primary">Tambah</a>

        <!-- Search Box -->
        <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
          <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
        </div>

        <!-- Edit Column Button -->
        <a href="#" class="btn btn-white">
          <i class="ti-pencil-alt"></i> Edit Column
        </a>
      </div>

        <!-- Tabs -->
       <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'users.php') ? 'active' : '' ?>" href="users.php">User</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'partner.php') ? 'active' : '' ?>" href="partner.php">Partner</a>
        </ul>

        <!-- Product Table -->
        <div class="row">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <table class="table table-striped" id="productTable">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="select-all"></th>
                      <th>Karyawan</th>
                      <th>Foto</th>
                      <th>Nomor Ponsel</th>
                      <th>Alamat</th>
                      <th>Role</th>
                      <th>Tempat Kerja</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Diaz</td>
                      <td><img src="path/to/image1.jpg" style="width: 50px; height: 50px;" alt="Image 1"></td>
                      <td>0897355472</td>
                      <td>Subang</td>
                      <td>Karyawan</td>
                      <td>Pusat</td>
                      <td>
                      <a href="" class="ms-2 text-dark" title="Detail Order">
                        <i class="mdi mdi-dots-vertical"></i>
                      </a>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-end" id="pagination">
            <li class="page-item disabled" id="prev">
              <a class="page-link bg-primary text-white" href="#">Previous</a>
            </li>
            <li class="page-item active">
              <a class="page-link bg-white text-primary" href="#">1</a>
            </li>
            <li class="page-item">
              <a class="page-link bg-white text-primary" href="#">2</a>
            </li>
            <li class="page-item" id="next">
              <a class="page-link bg-primary text-white" href="#">Next</a>
            </li>
          </ul>
        </nav>

<?php include '../layout/footer.php'; ?>
