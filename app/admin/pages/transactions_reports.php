
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
          <a class="nav-link text-primary <?= ($activePage == 'transactions_reports.php') ? 'active' : '' ?>" href="transactions_reports.php">Transactions <span class="badge bg-primary">42</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'outcome.php') ? 'active' : '' ?>" href="outcome.php">Outcome <span class="badge bg-primary">64</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'report.php') ? 'active' : '' ?>" href="report.php">Report <span class="badge bg-primary">8</span></a>
        </ul>


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
                      <th>Vehicle Code</th>
                      <th>No Tagihan</th>
                      <th>Status</th>
                      <th>Tanggal</th>
                      <th>Diskon</th>
                      <th>Total Harga</th>
                      <th>Pembayaran</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>T30045H</td>
                      <td>92763001635</td>
                      <td>Lunas</td>
                      <td>22 Maret 2025 09:00</td>
                      <td>0</td>
                      <td>5000000</td>
                      <td>5000000</td>
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
                      <td>M21088Z</td>
                      <td>92763001636</td>
                      <td>Lunas</td>
                      <td>23 Maret 2025 14:20</td>
                      <td>500000</td>
                      <td>15000000</td>
                      <td>14500000</td>
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
                      <td>B19876Y</td>
                      <td>92763001637</td>
                      <td>Cash</td>
                      <td>24 Maret 2025 11:30</td>
                      <td>0</td>
                      <td>8500000</td>
                      <td>8500000</td>
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
                      <td>F56743P</td>
                      <td>92763001638</td>
                      <td>Pending</td>
                      <td>25 Maret 2025 10:15</td>
                      <td>1000000</td>
                      <td>20000000</td>
                      <td>19000000</td>
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
                      <td>H34012Q</td>
                      <td>92763001639</td>
                      <td>Cash</td>
                      <td>26 Maret 2025 09:45</td>
                      <td>0</td>
                      <td>7200000</td>
                      <td>7200000</td>
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
                      <td>G87653L</td>
                      <td>92763001640</td>
                      <td>Cash</td>
                      <td>27 Maret 2025 16:00</td>
                      <td>250000</td>
                      <td>10000000</td>
                      <td>9750000</td>
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
                      <td>V65432X</td>
                      <td>92763001641</td>
                      <td>Kredit</td>
                      <td>28 Maret 2025 12:10</td>
                      <td>0</td>
                      <td>13000000</td>
                      <td>13000000</td>
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
                      <td>K45678N</td>
                      <td>92763001642</td>
                      <td>Cash</td>
                      <td>29 Maret 2025 15:45</td>
                      <td>300000</td>
                      <td>9500000</td>
                      <td>9200000</td>
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
                      <td>Z78901D</td>
                      <td>92763001643</td>
                      <td>Kredit</td>
                      <td>30 Maret 2025 13:30</td>
                      <td>500000</td>
                      <td>11000000</td>
                      <td>10500000</td>
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
                      <td>A12345C</td>
                      <td>92763001644</td>
                      <td>Cash</td>
                      <td>31 Maret 2025 10:00</td>
                      <td>0</td>
                      <td>7800000</td>
                      <td>7800000</td>
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

      

