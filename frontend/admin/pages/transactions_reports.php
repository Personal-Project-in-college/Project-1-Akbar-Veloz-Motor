
<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>


<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Transactions</h3>

    <!-- Actions & Navigation -->
    <div class="mb-3">
      <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
        <a href="add_transactions.php" class="btn btn-primary">Tambah</a>

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
          <a class="nav-link text-primary <?= ($activePage == 'report.php') ? 'active' : '' ?>" href="report.php">Report<span class="badge bg-primary">99+</span></a>
        </ul>

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
<!-- Table -->
        <div class="row">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <table class="table table-striped" id="productTable">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="select-all"></th>
                      <th>Vehicle Code</th>
                      <th>No Tagihan</th>
                      <th>Status</th>
                      <th>Tanggal</th>
                      <th>Diskon</th>
                      <th>Total Harga</th>
                      <th>Pembayaran</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>T30045H</td>
                      <td>92763001635</td>
                      <td>Cash</td>
                      <td>22 Maret 2025 09:00</td>
                      <td>0</td>
                      <td>5000000</td>
                      <td>5000000</td>
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
</div>

<?php include '../layout/footer.php'; ?>
