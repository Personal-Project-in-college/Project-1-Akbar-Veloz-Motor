<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Outcome</h3>

    <!-- Actions & Navigation -->
    <div class="mb-3">
      <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
        <a href="tambah_produk.php" class="btn btn-primary">Tambah</a>

        <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
          <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
        </div>

        <a href="#" class="btn btn-white">
          <i class="ti-pencil-alt"></i> Edit Column
        </a>
      </div>

      <!-- Tabs -->
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'transactions_reports.php') ? 'active' : '' ?>" href="transactions_reports.php">Transactions</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'outcome.php') ? 'active' : '' ?>" href="outcome.php">Outcome</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'report.php') ? 'active' : '' ?>" href="report.php">Report <span class="badge bg-primary">99+</span></a>
        </li>
      </ul>
    </div>

    <!-- Card -->
    <div class="row mt-4">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <!-- Filter Form -->
            <h4 class="mb-4">Filter Data</h4>
            <form class="row g-3 align-items-end mb-4">
              <div class="col-md-6">
                <label for="bulanTahun" class="form-label">Bulan dan Tahun</label>
                <input type="month" class="form-control" id="bulanTahun" name="bulanTahun">
              </div>
            </form>

            <!-- Tabel -->
            <table class="table table-striped" id="productTable">
              <thead>
                <tr>
                  <th><input type="checkbox" id="select-all"></th>
                  <th>Tanggal</th>
                  <th>Jenis Pengeluaran</th>
                  <th>Keterangan</th>
                  <th>Nominal</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="checkbox" class="select-row"></td>
                  <td>22 Maret 2025 09:00</td>
                  <td>Service</td>
                  <td>Perawatan Kendaraan</td>
                  <td>100000000</td>
                </tr>
                <!-- Tambahkan baris data lainnya di sini -->
              </tbody>
            </table>

            <!-- Pagination (pindahkan ke dalam card-body) -->
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-end mt-4" id="pagination">
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

          </div> <!-- /.card-body -->
        </div> <!-- /.card -->
      </div>
    </div>
</div> <!-- /.main-panel -->

<?php include '../layout/footer.php'; ?>
