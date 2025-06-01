<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>


<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Report</h3>

      <!-- Actions -->
      <div class="mb-3">
        <!-- Search Box -->
        <div class="mb-3" style="width: 100%">
          <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
        </div>

      <!-- Tabs -->
       <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'transactions_reports.php') ? 'active' : '' ?>" href="transactions_reports.php">Transactions <span class="badge bg-primary">42</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'outcome.php') ? 'active' : '' ?>" href="outcome.php">Outcome <span class="badge bg-primary">64</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'report.php') ? 'active' : '' ?>" href="report.php">Report <span class="badge bg-primary">8</span></a>
        </ul>

      <!-- Filter -->
        <div class="container mt-4">
          <div class="card">
            <div class="card-body">
              <h4 class="mb-4">Filter Data</h4>
              <form class="row g-3 align-items-end">
                <div class="col-md-4">
                  <label for="bulanTahun" class="form-label">Bulan dan Tahun</label>
                  <input type="month" class="form-control" id="bulanTahun">
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary">Update Laporan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

<?php include '../layout/footer.php'; ?>
