<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">Products</h3>

        <!-- Actions -->
        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
        <!-- Tambahkan Produk Button -->
        <a href="add_products.php" class="btn btn-primary">Tambah</a>

        <!-- Search Box -->
        <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
          <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
        </div>

        
      </div>

        <!-- Tabs -->
       <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'products.php') ? 'active' : '' ?>" href="products.php">Products <span class="badge bg-primary">2</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'services.php') ? 'active' : '' ?>" href="services.php">Services <span class="badge bg-primary">7</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'orders.php') ? 'active' : '' ?>" href="orders.php">Orders <span class="badge bg-primary">99+</span></a>
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
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td><input type="checkbox" class="select-row"></td>
                    <td>B123KLM</td>
                    <td><img src="path/to/image1.jpg" style="width: 50px; height: 50px;" alt="Image 1"></td>
                    <td>Honda Vario 150</td>
                    <td>Motor</td>
                    <td>23jt</td>
                    <td>Unit Siap pakai</td>
                    <td><span class="badge bg-success">Available</span></td>
                    <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                    <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                      style="width: 28px; height: 28px; border-radius: 4px;">
                      <i class="mdi mdi-pencil"></i>
                    </button>
                    <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                      style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                      <i class="mdi mdi-delete" style="color: white;"></i>
                    </button>
                    </td>
                  <!-- </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>D456XYZ</td>
                      <td><img src="path/to/image2.jpg" style="width: 50px; height: 50px;" alt="Image 2"></td>
                      <td>Toyota Avanza 2020</td>
                      <td>Mobil</td>
                      <td>150jt</td>
                      <td>Baru diservis</td>
                      <td><span class="badge bg-warning">Test Drive</span></td>
                      <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>E789JKL</td>
                      <td><img src="path/to/image3.jpg" style="width: 50px; height: 50px;" alt="Image 3"></td>
                      <td>Suzuki Ertiga GL</td>
                      <td>Mobil</td>
                      <td>135jt</td>
                      <td>Perlu pengecekan oli</td>
                      <td><span class="badge bg-danger">Service</span></td>
                     <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>F321BCA</td>
                      <td><img src="path/to/image4.jpg" style="width: 50px; height: 50px;" alt="Image 4"></td>
                      <td>Yamaha NMAX 2023</td>
                      <td>Motor</td>
                      <td>34jt</td>
                      <td>Unit premium</td>
                      <td><span class="badge bg-success">Available</span></td>
                      <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>G654LMN</td>
                      <td><img src="path/to/image5.jpg" style="width: 50px; height: 50px;" alt="Image 5"></td>
                      <td>Daihatsu Sigra</td>
                      <td>Mobil</td>
                      <td>120jt</td>
                      <td>Siap jalan jauh</td>
                      <td><span class="badge bg-warning">Test Drive</span></td>
                      <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>H111QWE</td>
                      <td><img src="path/to/image6.jpg" style="width: 50px; height: 50px;" alt="Image 6"></td>
                      <td>Kawasaki Ninja RR</td>
                      <td>Motor</td>
                      <td>48jt</td>
                      <td>Butuh servis rem</td>
                      <td><span class="badge bg-danger">Service</span></td>
                      <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>I222RTY</td>
                      <td><img src="path/to/image7.jpg" style="width: 50px; height: 50px;" alt="Image 7"></td>
                      <td>Honda Beat Street</td>
                      <td>Motor</td>
                      <td>17jt</td>
                      <td>Promo bulan ini</td>
                      <td><span class="badge bg-success">Available</span></td>
                      <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>J333UIO</td>
                      <td><img src="path/to/image8.jpg" style="width: 50px; height: 50px;" alt="Image 8"></td>
                      <td>Suzuki Carry Pickup</td>
                      <td>Mobil</td>
                      <td>85jt</td>
                      <td>Kondisi prima</td>
                      <td><span class="badge bg-success">Available</span></td>
                      <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>K444PAS</td>
                      <td><img src="path/to/image9.jpg" style="width: 50px; height: 50px;" alt="Image 9"></td>
                      <td>Mitsubishi Xpander</td>
                      <td>Mobil</td>
                      <td>210jt</td>
                      <td>Masih garansi</td>
                      <td><span class="badge bg-success">Available</span></td>
                      <td style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                      <button title="Edit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px;">
                        <i class="mdi mdi-pencil"></i>
                      </button>
                      <button title="Delete" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center"
                        style="width: 28px; height: 28px; border-radius: 4px; color: white;">
                        <i class="mdi mdi-delete" style="color: white;"></i>
                      </button>
                      </td>
                      </tr>
                    </tr> -->
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
