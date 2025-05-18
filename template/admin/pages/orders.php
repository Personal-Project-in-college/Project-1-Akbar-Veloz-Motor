<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">Produk</h3>

       <!-- Actions -->
      <div class="d-flex align-items-center flex-wrap mb-3 gap-2">

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
          <a class="nav-link text-primary <?= ($activePage == 'produk.php') ? 'active' : '' ?>" href="produk.php">Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'layanan.php') ? 'active' : '' ?>" href="layanan.php">Layanan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'orders.php') ? 'active' : '' ?>" href="orders.php">Order <span class="badge bg-primary">99+</span></a>
        </li>
         <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'laporan.php') ? 'active' : '' ?>" href="laporan.php">Laporan</a>
        </li>
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
                      <th>Customer</th>
                      <th>Nomor Ponsel</th>
                      <th>Alamat</th>
                      <th>Kendaraan</th>
                      <th>Tipe</th>
                      <th>Request</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                    <td><input type="checkbox" class="select-row"></td>
                    <td>Zaki</td>
                    <td>083100099</td>
                    <td>Cimahi</td>
                    <td>Hillux</td>
                    <td>Sport</td>
                    <td>
                      <span class="badge bg-success">Transaksi</span>
                      <a href="detail_order.php?id=1" class="ms-2 text-dark" title="Detail Order">
                        <i class="mdi mdi-dots-vertical"></i>
                      </a>
                    </td>
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
