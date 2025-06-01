<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">User</h3>

       <!-- Actions -->
      <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
        <!-- Tambahkan Produk Button -->
        <a href="add_user.php" class="btn btn-primary">Tambah</a>

        <!-- Search Box -->
        <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
          <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
        </div>

      </div>

        <!-- Tabs -->
       <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'users.php') ? 'active' : '' ?>" href="users.php">User</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'partner.php') ? 'active' : '' ?>" href="partner.php">Partner</a>
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
                      <th>Karyawan</th>
                      <th>Foto</th>
                      <th>Nomor Ponsel</th>
                      <th>Alamat</th>
                      <th>Role</th>
                      <th>Tempat Kerja</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="checkbox" class="select-row"></td>
                      <td>Diaz</td>
                      <td><img src="path/to/image1.jpg" style="width: 50px; height: 50px;" alt="Image 1"></td>
                      <td>0897355472</td>
                      <td>Subang</td>
                      <td>Karyawan</td>
                      <td>Pusat</td>
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
                    <td>Diaz</td>
                    <td><img src="path/to/image1.jpg" style="width: 50px; height: 50px;" alt="Image 1"></td>
                    <td>0897355472</td>
                    <td>Subang</td>
                    <td>Karyawan</td>
                    <td>Pusat</td>
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
                    <td><img src="path/to/image2.jpg" style="width: 50px; height: 50px;" alt="Image 2"></td>
                    <td>0812345678</td>
                    <td>Bandung</td>
                    <td>Admin</td>
                    <td>Cabang A</td>
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
                    <td>Budi</td>
                    <td><img src="path/to/image3.jpg" style="width: 50px; height: 50px;" alt="Image 3"></td>
                    <td>0898765432</td>
                    <td>Jakarta</td>
                    <td>Karyawan</td>
                    <td>Cabang B</td>
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
                    <td>Citra</td>
                    <td><img src="path/to/image4.jpg" style="width: 50px; height: 50px;" alt="Image 4"></td>
                    <td>0822334455</td>
                    <td>Bekasi</td>
                    <td>Manager</td>
                    <td>Pusat</td>
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
                    <td>Dodi</td>
                    <td><img src="path/to/image5.jpg" style="width: 50px; height: 50px;" alt="Image 5"></td>
                    <td>0833221100</td>
                    <td>Depok</td>
                    <td>Teknisi</td>
                    <td>Cabang C</td>
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
                    <td>Eka</td>
                    <td><img src="path/to/image6.jpg" style="width: 50px; height: 50px;" alt="Image 6"></td>
                    <td>0811223344</td>
                    <td>Tangerang</td>
                    <td>Karyawan</td>
                    <td>Pusat</td>
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
                    <td>Fajar</td>
                    <td><img src="path/to/image7.jpg" style="width: 50px; height: 50px;" alt="Image 7"></td>
                    <td>0888997766</td>
                    <td>Yogyakarta</td>
                    <td>Admin</td>
                    <td>Cabang A</td>
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
                    <td>Gita</td>
                    <td><img src="path/to/image8.jpg" style="width: 50px; height: 50px;" alt="Image 8"></td>
                    <td>0877665544</td>
                    <td>Semarang</td>
                    <td>Karyawan</td>
                    <td>Cabang B</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" class="select-row"></td>
                    <td>Hadi</td>
                    <td><img src="path/to/image9.jpg" style="width: 50px; height: 50px;" alt="Image 9"></td>
                    <td>0866554433</td>
                    <td>Surabaya</td>
                    <td>Teknisi</td>
                    <td>Cabang C</td>
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
                    <td>Intan</td>
                    <td><img src="path/to/image10.jpg" style="width: 50px; height: 50px;" alt="Image 10"></td>
                    <td>0855443322</td>
                    <td>Malang</td>
                    <td>Manager</td>
                    <td>Pusat</td>
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
                    <td>Joko</td>
                    <td><img src="path/to/image11.jpg" style="width: 50px; height: 50px;" alt="Image 11"></td>
                    <td>0844332211</td>
                    <td>Garut</td>
                    <td>Karyawan</td>
                    <td>Cabang A</td>
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
