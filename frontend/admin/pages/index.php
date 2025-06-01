<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<<<<<<< HEAD
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">Produk</h3>

       <!-- Actions -->
      <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
        <!-- Tambahkan Produk Button -->
        <a href="tambah_produk.php" class="btn btn-primary">Tambahkan Users</a>

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
                      <th>Karyawan/th>
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
                    </td></tr>
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
=======

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Partner Representative</h3>
    <!-- Partner Representative table -->
    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card" style="border-radius: 15px; overflow: hidden;">
          <div class="card-body">
            <table class="table table-striped" style="border-radius: 15px; overflow: hidden; id=" productTable">
              <tbody>
                <tr>
                  <td>
                    <img src="../src/assets/images/jamal.png" style="width: 50px; height: 50px; border-radius: 50%;"
                      alt="Profile Image">
                  </td>
                  <td>Epi Halimah</td>
                  <td><span class="badge bg-success">Services</span></td>
                  <td>Oppressor MK</td>
                  <td>1</td>
                  <td>
                    <button class="btn btn-link p-0" title="Options">
                      <i class="mdi mdi-dots-vertical"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <img src="../src/assets/images/zidan.jpg" style="width: 50px; height: 50px; border-radius: 50%;"
                      alt="Profile Image">
                  </td>
                  <td>Moch Zidan Sudrajat</td>
                  <td><span class="badge bg-warning">Pending</span></td>
                  <td>Honda Vario</td>
                  <td>2</td>
                  <td>
                    <button class="btn btn-link p-0" title="Options">
                      <i class="mdi mdi-dots-vertical"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <img src="../src/assets/images/goku.jpg" style="width: 50px; height: 50px; border-radius: 50%;"
                      alt="Profile Image">
                  </td>
                  <td>Zacki Syaeful B</td>
                  <td><span class="badge bg-danger">Problem</span></td>
                  <td>Honda Vario</td>
                  <td>2</td>
                  <td>
                    <button class="btn btn-link p-0" title="Options">
                      <i class="mdi mdi-dots-vertical"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <img src="../src/assets/images/Diaz.jpeg" style="width: 50px; height: 50px; border-radius: 50%;"
                      alt="Profile Image">
                  </td>
                  <td>M. Dhiyul</td>
                  <td><span class="badge bg-success">Services</span></td>
                  <td>Honda Vario</td>
                  <td>2</td>
                  <td>
                    <button class="btn btn-link p-0" title="Options">
                      <i class="mdi mdi-dots-vertical"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <img src="../src/assets/images/Farhan.jpg" style="width: 50px; height: 50px; border-radius: 50%;"
                      alt="Profile Image">
                  </td>
                  <td>Farhan Ginting</td>
                  <td><span class="badge bg-warning">Pending</span></td>
                  <td>Honda Vario</td>
                  <td>2</td>
                  <td>
                    <button class="btn btn-link p-0" title="Options">
                      <i class="mdi mdi-dots-vertical"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- end of Partner Representative table -->
    <!-- Info Boxes -->
    <div class="row mb-4">
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body text-center">
            <div class="icon mb-2">
              <i class="mdi mdi-cash-multiple"></i>
            </div>
            <h5>Total Penghasilan</h5>
            <p>3.5k</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body text-center">
            <div class="icon mb-2">
              <i class="mdi mdi-cube-outline"></i>
            </div>
            <h5>Total Penjualan</h5>
            <p>3 Unit</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body text-center">
            <div class="icon mb-2">
              <i class="mdi mdi-chart-line"></i>
            </div>
            <h5>Penjualan Bulanan</h5>
            <p>Rp. 11123131</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body text-center">
            <div class="icon mb-2">
              <i class="mdi mdi-cash-minus"></i>
            </div>
            <h5>Pengeluaran Bulanan</h5>
            <p>-Rp. 123131</p>
          </div>
        </div>
      </div>
    </div>

    <div style="display: flex; flex-direction: row; gap:0px 20px;">
        <!-- Bar Chart Penjualan -->
      <div class="row" style="width: 80%;">
        <div class="col-lg-6 grid-margin stretch-card" style="width: 100%;">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Pusat</h4>
              <canvas id="barChart-1"></canvas>
            </div>
          </div>
        </div>
      </div>

          <!-- Bar Chart Penjualan -->
      <div class="row"  style="width: 120%;">
        <div class="col-lg-6 grid-margin stretch-card" style="width: 100%;">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Cabang</h4>
              <canvas id="barChart-2"></canvas>
            </div>
          </div>
        </div>
      </div>


    </div>

<!-- Plugin js for this page -->
<script src="../src/assets/vendors/chart.js/chart.umd.js"></script>
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="../src/assets/js/index.js"></script>
<!-- End custom js for this page-->

<?php include '../layout/footer.php'; ?>
>>>>>>> 703cd13f51429092b74dc97b39f175015cecc1e9
