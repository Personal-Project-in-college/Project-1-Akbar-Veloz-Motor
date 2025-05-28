<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">Service</h3>

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
                      <th>Status</th>
                      <th>Ditangani Oleh</th>
                      <th>Harga</th>
                      <th>Deskripsi</th>
                      <th>Tanggal</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>B123KLM</td>
                      <td>Diperbaiki</td>
                      <td>Farhan</td>
                      <td>100jt</td>
                      <td>Meledak</td>
                      <td>25 Desember 2025</td>
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
                      <td>D456XYZ</td>
                      <td>Selesai</td>
                      <td>Ani</td>
                      <td>75jt</td>
                      <td>Ganti oli & rem</td>
                      <td>10 Januari 2025</td>
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
                      <td>E789MNO</td>
                      <td>Menunggu Sparepart</td>
                      <td>Rudi</td>
                      <td>120jt</td>
                      <td>Kerusakan mesin</td>
                      <td>5 Februari 2025</td>
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
                      <td>F321JKL</td>
                      <td>Diperbaiki</td>
                      <td>Siti</td>
                      <td>90jt</td>
                      <td>Cat ulang bodi</td>
                      <td>17 Maret 2025</td>
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
                      <td>G654TUV</td>
                      <td>Selesai</td>
                      <td>Bayu</td>
                      <td>60jt</td>
                      <td>Service AC</td>
                      <td>28 April 2025</td>
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
                      <td>H987PQR</td>
                      <td>Diperbaiki</td>
                      <td>Lina</td>
                      <td>130jt</td>
                      <td>Perbaikan transmisi</td>
                      <td>14 Mei 2025</td>
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
                      <td>I234EFG</td>
                      <td>Menunggu Konfirmasi</td>
                      <td>Joko</td>
                      <td>50jt</td>
                      <td>Periksa sistem kelistrikan</td>
                      <td>30 Mei 2025</td>
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
                      <td>J567HIJ</td>
                      <td>Selesai</td>
                      <td>Dewi</td>
                      <td>70jt</td>
                      <td>Ganti ban & spooring</td>
                      <td>12 Juni 2025</td>
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
                      <td>K890LMN</td>
                      <td>Diperbaiki</td>
                      <td>Rian</td>
                      <td>85jt</td>
                      <td>Perbaikan suspensi</td>
                      <td>8 Juli 2025</td>
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
                      <td>L123OPQ</td>
                      <td>Menunggu Sparepart</td>
                      <td>Nina</td>
                      <td>95jt</td>
                      <td>Masalah injeksi bahan bakar</td>
                      <td>20 Agustus 2025</td>
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
                      <td>M456RST</td>
                      <td>Selesai</td>
                      <td>Deni</td>
                      <td>110jt</td>
                      <td>Perbaikan total</td>
                      <td>2 September 2025</td>
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
                      <td>N789UVW</td>
                      <td>Menunggu Konfirmasi</td>
                      <td>Andi</td>
                      <td>65jt</td>
                      <td>Cek sistem pendingin</td>
                      <td>15 September 2025</td>
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
                      <td>O321QWE</td>
                      <td>Diperbaiki</td>
                      <td>Vina</td>
                      <td>80jt</td>
                      <td>Reset ECU</td>
                      <td>29 September 2025</td>
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
                      <td>P654RTY</td>
                      <td>Selesai</td>
                      <td>Fajar</td>
                      <td>105jt</td>
                      <td>Ganti radiator</td>
                      <td>3 Oktober 2025</td>
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
                      <td>Q987UIO</td>
                      <td>Diperbaiki</td>
                      <td>Maya</td>
                      <td>98jt</td>
                      <td>Kalibrasi sistem sensor</td>
                      <td>18 Oktober 2025</td>
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
                      <td>R210ASD</td>
                      <td>Menunggu Sparepart</td>
                      <td>Ilham</td>
                      <td>55jt</td>
                      <td>Pemrograman ulang kunci</td>
                      <td>27 Oktober 2025</td>
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
