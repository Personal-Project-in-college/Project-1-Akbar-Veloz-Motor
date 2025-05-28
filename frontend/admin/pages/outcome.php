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
              <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
              </div>

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


          <!-- Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">

                  <!-- Filter Form -->
                  <h4 class="mb-4">Filter Data</h4>
                  <form class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                      <label for="bulanTahun" class="form-label">Tanggal</label>
                      <input type="date" class="form-control" id="bulanTahun" name="bulanTahun">
                    </div>
                    <div class="col-md-6">
                    <label for="statusTransaksi" class="form-label">Status Transaksi</label>
                    <select class="form-select" id="statusTransaksi" name="statusTransaksi">
                      <option selected disabled>Pilih Status</option>
                      <option value="Cash">Cash</option>
                      <option value="Kredit">Kredit</option>
                      <option value="Pending">Pending</option>
                      <option value="Lunas">Lunas</option>
                      <option value="Batal">Batal</option>
                    </select>
                  </div>
                  </form>

                  <table class="table table-striped" id="productTable">
                  <thead>
                  <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Tanggal</th>
                    <th>Jenis Pengeluaran</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input type="checkbox" class="select-row"></td>
                    <td>22 Maret 2025 09:00</td>
                    <td>Service</td>
                    <td>Perawatan Kendaraan</td>
                    <td>100000000</td>
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
                      <td>22 Maret 2025 09:00</td>
                      <td>Service</td>
                      <td>Perawatan Kendaraan</td>
                      <td>100000000</td>
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
                      <td>23 Maret 2025 13:15</td>
                      <td>Bahan Bakar</td>
                      <td>Pengisian BBM armada</td>
                      <td>500000</td>
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
                      <td>24 Maret 2025 10:30</td>
                      <td>Gaji</td>
                      <td>Gaji bulanan staf</td>
                      <td>15000000</td>
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
                      <td>25 Maret 2025 08:00</td>
                      <td>Listrik</td>
                      <td>Tagihan listrik kantor</td>
                      <td>1200000</td>
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
                      <td>26 Maret 2025 11:45</td>
                      <td>ATK</td>
                      <td>Pembelian alat tulis</td>
                      <td>350000</td>
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
                      <td>27 Maret 2025 15:20</td>
                      <td>Internet</td>
                      <td>Langganan bulanan WiFi</td>
                      <td>800000</td>
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
                      <td>28 Maret 2025 09:50</td>
                      <td>Service</td>
                      <td>Penggantian oli dan filter</td>
                      <td>750000</td>
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
                      <td>29 Maret 2025 14:10</td>
                      <td>Konsumsi</td>
                      <td>Snack rapat mingguan</td>
                      <td>200000</td>
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
                      <td>30 Maret 2025 16:30</td>
                      <td>Pemasaran</td>
                      <td>Iklan di media sosial</td>
                      <td>3000000</td>
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
                      <td>31 Maret 2025 12:00</td>
                      <td>Perlengkapan</td>
                      <td>Pembelian perlengkapan kantor</td>
                      <td>1000000</td>
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


           