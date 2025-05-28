<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">Orders</h3>

        <!-- Actions -->

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
                      <th>Customer</th>
                      <th>Nomor Ponsel</th>
                      <th>Alamat</th>
                      <th>Kendaraan</th>
                      <th>Tipe</th>
                      <th>Request</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Zaki</td>
                      <td>083100099</td>
                      <td>Cimahi</td>
                      <td>Hillux</td>
                      <td>Mobil - Sport</td>
                      <td>
                        <span class="badge bg-success">Transaksi</span>
                        <a href="details_orders.php?id=1" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
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
                      <td>Ani</td>
                      <td>082112345678</td>
                      <td>Subang</td>
                      <td>Beat</td>
                      <td>Motor - Matic</td>
                      <td>
                        <span class="badge bg-warning">Pending</span>
                        <a href="details_orders.php?id=2" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
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
                      <td>Bayu</td>
                      <td>081390012345</td>
                      <td>Purwakarta</td>
                      <td>Fortuner</td>
                      <td>Mobil - SUV</td>
                      <td>
                        <span class="badge bg-success">Transaksi</span>
                        <a href="details_orders.php?id=3" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
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
                      <td>Dina</td>
                      <td>087722334455</td>
                      <td>Bandung</td>
                      <td>Vario</td>
                      <td>Motor - Matic</td>
                      <td>
                        <span class="badge bg-danger">Batal</span>
                        <a href="details_orders.php?id=4" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
                      </td>
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
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Rudi</td>
                      <td>085566778899</td>
                      <td>Karawang</td>
                      <td>CR-V</td>
                      <td>Mobil - SUV</td>
                      <td>
                        <span class="badge bg-warning">Pending</span>
                        <a href="details_orders.php?id=5" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
                      </td>
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
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Sari</td>
                      <td>081234567890</td>
                      <td>Majalengka</td>
                      <td>PCX 160</td>
                      <td>Motor - Maxi</td>
                      <td>
                        <span class="badge bg-success">Transaksi</span>
                        <a href="details_orders.php?id=6" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
                      </td>
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
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Joko</td>
                      <td>088812345678</td>
                      <td>Garut</td>
                      <td>Rush</td>
                      <td>Mobil - SUV</td>
                      <td>
                        <span class="badge bg-success">Transaksi</span>
                        <a href="details_orders.php?id=7" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
                      </td>
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
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Lina</td>
                      <td>089911223344</td>
                      <td>Tasikmalaya</td>
                      <td>Scoopy</td>
                      <td>Motor - Retro</td>
                      <td>
                        <span class="badge bg-danger">Batal</span>
                        <a href="details_orders.php?id=8" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
                      </td>
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
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Dedi</td>
                      <td>081811223355</td>
                      <td>Sumedang</td>
                      <td>Xpander</td>
                      <td>Mobil - MPV</td>
                      <td>
                        <span class="badge bg-warning">Pending</span>
                        <a href="details_orders.php?id=9" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
                      </td>
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
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Rina</td>
                      <td>082144556677</td>
                      <td>Lembang</td>
                      <td>NMAX</td>
                      <td>Motor - Maxi</td>
                      <td>
                        <span class="badge bg-success">Transaksi</span>
                        <a href="details_orders.php?id=10" class="ms-2 text-dark" title="Detail Order">
                          <i class="mdi mdi-dots-vertical"></i>
                        </a>
                      </td>
                      </td>
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
