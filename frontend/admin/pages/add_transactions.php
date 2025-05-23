<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="mb-4">Tambah Laporan</h3>

    

<?php include '../layout/footer.php'; ?>
  <!-- Card Wrapper -->
      <div class="card">
        <div class="card-body">
          <form>
            <div class="row">
              <!-- Kolom Kiri -->
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="jenis_laporan" class="form-label">Jenis Laporan</label>
                  <input type="text" class="form-control" id="jenis_laporan" placeholder="Masukan Jenis Laporan (Transaksi / Pengeluaran)">
                </div>

                <div class="mb-3">
                  <label for="vehicle_code" class="form-label">Vehicle Code</label>
                  <input type="text" class="form-control" id="vehicle_code" placeholder="Input kode kendaraan">
                </div>

                <div class="mb-3">
                  <label for="invoice" class="form-label">No. Invoice</label>
                  <input type="text" class="form-control" id="invoice" placeholder="Masukan nomor tagihan">
                </div>

                <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status">
                    <option selected>Mohon pilih!</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum">Belum Lunas</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="diskon" class="form-label">Diskon</label>
                  <input type="text" class="form-control" id="diskon" placeholder="Masukan potongan harga">
                </div>

                <div class="mb-3">
                  <label for="total_harga" class="form-label">Total Harga</label>
                  <input type="text" class="form-control" id="total_harga" placeholder="Automatic fill" disabled>
                </div>

                <div class="mb-3">
                  <label for="pembayaran" class="form-label">Pembayaran</label>
                  <input type="text" class="form-control" id="pembayaran" placeholder="Masukan jumlah uang dibayar">
                </div>
              </div>

              <!-- Kolom Kanan -->
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="jenis_pengeluaran" class="form-label">Jenis Pengeluaran</label>
                  <input type="text" class="form-control" id="jenis_pengeluaran" placeholder="Input kode kendaraan">
                </div>

                <div class="mb-3">
                  <label for="keterangan" class="form-label">Keterangan</label>
                  <input type="text" class="form-control" id="keterangan" placeholder="Masukan nomor tagihan">
                </div>

                <div class="mb-3">
                  <label for="nominal" class="form-label">Nominal</label>
                  <select class="form-select" id="nominal">
                    <option selected>Mohon pilih!</option>
                    <option value="100000">Rp 100.000</option>
                    <option value="200000">Rp 200.000</option>
                    <option value="lainnya">Lainnya...</option>
                  </select>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit</button>
          </form>
        </div>
      </div>