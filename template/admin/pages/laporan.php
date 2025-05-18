<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Laporan</h3>

 <!-- Actions -->
<div class="mb-3">
  <!-- Search Box -->
  <div class="mb-3" style="width: 100%">
    <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link text-primary <?= ($activePage == 'produk.php') ? 'active' : '' ?>" href="produk.php">Produk</a>    
    </li>
    <li class="nav-item">
      <a class="nav-link text-primary <?= ($activePage == 'layanan.php') ? 'active' : '' ?>" href="layanan.php">Layanan</a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-primary <?= ($activePage == 'orders.php') ? 'active' : '' ?>" href="order.php">Order <span class="badge bg-primary">99+</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-primary <?= ($activePage == 'laporan.php') ? 'active' : '' ?>" href="laporan.php">Laporan</a>
    </li>
  </ul>
</div>

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

<br>

<center><h4>ini nanti tampil PDF?</h4><center>

<?php include '../layout/footer.php'; ?>
