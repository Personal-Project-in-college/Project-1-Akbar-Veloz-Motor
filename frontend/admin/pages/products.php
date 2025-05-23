<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">Produk</h3>

       <!-- Actions -->
      <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
        <!-- Tambahkan Produk Button -->
        <a href="add_products.php" class="btn btn-primary">Tambah</a>

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
          <a class="nav-link text-primary <?= ($activePage == 'products.php') ? 'active' : '' ?>" href="products.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'services.php') ? 'active' : '' ?>" href="services.php">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'orders.php') ? 'active' : '' ?>" href="orders.php">Orders<span class="badge bg-primary">99+</span></a>
        </ul>

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
                      <th>Image & Description</th>
                      <th>Brand & Model</th>
                      <th>Category</th>
                      <th>Cost</th>
                      <th>Description</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image1.jpg" style="width: 50px; height: 50px;" alt="Image 1"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Available</td>
                      <td><span class="badge bg-success">Available</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image2.jpg" style="width: 50px; height: 50px;" alt="Image 2"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Test Drive</td>
                      <td><span class="badge bg-warning">Test Drive</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image3.jpg" style="width: 50px; height: 50px;" alt="Image 3"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Service</td>
                      <td><span class="badge bg-danger">Service</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image1.jpg" style="width: 50px; height: 50px;" alt="Image 1"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Available</td>
                      <td><span class="badge bg-success">Available</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image2.jpg" style="width: 50px; height: 50px;" alt="Image 2"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Test Drive</td>
                      <td><span class="badge bg-warning">Test Drive</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image3.jpg" style="width: 50px; height: 50px;" alt="Image 3"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Service</td>
                      <td><span class="badge bg-danger">Service</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image3.jpg" style="width: 50px; height: 50px;" alt="Image 3"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Service</td>
                      <td><span class="badge bg-danger">Service</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image1.jpg" style="width: 50px; height: 50px;" alt="Image 1"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Available</td>
                      <td><span class="badge bg-success">Available</span></td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td><img src="path/to/image2.jpg" style="width: 50px; height: 50px;" alt="Image 2"></td>
                      <td>Oppressor MK</td>
                      <td>MOTOR</td>
                      <td>100jt</td>
                      <td>Test Drive</td>
                      <td><span class="badge bg-warning">Test Drive</span></td>
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
