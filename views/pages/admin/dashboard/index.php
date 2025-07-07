<?php
session_start();
// 🔎 Melakukan Pengecekan Apakah Sudah Login atau Belum
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

$date_today = date('Y-m-d');
$month_now = date('Y-m');

// 1. Total Penghasilan Harian
$stmt = $koneksi->prepare("
    SELECT SUM(t.grand_total - v.lowest_price) AS profit_today
    FROM transactions t
    JOIN orders o ON t.order_id = o.id
    JOIN vehicles v ON o.vehicle_id = v.id
    WHERE DATE(t.created_at) = ?
");
$stmt->execute([$date_today]);
$daily_profit = $stmt->fetchColumn() ?? 0;

// 2. Total Kendaraan (status ≠ 'sold')
$stmt = $koneksi->query("SELECT COUNT(*) FROM vehicles WHERE status != 'sold'");
$vehicle_count = $stmt->fetchColumn();

// 3. Penjualan Bulanan
$stmt = $koneksi->prepare("
    SELECT SUM(t.grand_total - v.lowest_price) AS monthly_profit
    FROM transactions t
    JOIN orders o ON t.order_id = o.id
    JOIN vehicles v ON o.vehicle_id = v.id
    WHERE DATE_FORMAT(t.created_at, '%Y-%m') = ?
");
$stmt->execute([$month_now]);
$monthly_profit = $stmt->fetchColumn() ?? 0;

// 4. Total Order Bulanan (status = 'finished')
$stmt = $koneksi->prepare("
    SELECT COUNT(*) FROM orders
    WHERE status = 'finished' AND DATE_FORMAT(created_at, '%Y-%m') = ?
");
$stmt->execute([$month_now]);
$monthly_finished_orders = $stmt->fetchColumn() ?? 0;


include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div id="floating-alert-container" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999; max-width: 300px;"></div>

<!-- Main Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="mdi mdi-cash-multiple"></i>
                        </div>
                        <h5>Total Penghasilan Harian</h5>
                        <p>Rp <?= number_format($daily_profit, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="mdi mdi-chart-line"></i>
                        </div>
                        <h5>Penjualan Bulanan</h5>
                        <p>Rp <?= number_format($monthly_profit, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="mdi mdi-cash-minus"></i>
                        </div>
                        <h5>Total Pesanan Bulanan</h5>
                        <p><?= $monthly_finished_orders ?> Pesanan Selesai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="mdi mdi-cube-outline"></i>
                        </div>
                        <h5>Total Kendaraan</h5>
                        <p><?= $vehicle_count ?> Unit</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partner Representative table -->
        <h3 class="mb-4">Transaksi Terbaru</h3>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body overflow-auto">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal Pesan</th>
                                    <th>Kendaraan</th>
                                    <th>Customer</th>
                                    <th>Total Pembayaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="transactionTableBody" >
                                <tr>
                                    <td colspan="5" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end of Partner Representative table-->


        <!-- <div style="display: flex; flex-direction: row; gap:0px 20px;">
            <div class="row" style="width: 80%;">
                <div class="col-lg-6 grid-margin stretch-card" style="width: 100%;">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Pusat</h4>
                            <canvas id="barChart-1"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="width: 120%;">
                <div class="col-lg-6 grid-margin stretch-card" style="width: 100%;">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Cabang</h4>
                            <canvas id="barChart-2"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body">
                        <table class="table table-striped" style="width: 100%; border-radius: 15px; overflow: hidden;" id="lastOrdersTable">
                            <thead>
                                <tr>
                                    <th>Last Orders</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="order-item">
                                    <td>Ada order tersedia, ambil sekarang!</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link p-0 dropdown-toggle" title="Options" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="orders.php">Go to Orders</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->
        <script src="../assets/js/index.js"></script>

        <?php include '../layout/footer.php'; ?>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('transactionTableBody');

        function loadBrands() {
            fetch(`ajaxTransactionList.php`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                });
        }

        // Load pertama
        loadBrands();
    </script>

    <script>
        function bindDeleteEvents() {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    fetch('softDelete.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id=' + encodeURIComponent(id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                loadBrands(); // reload tabel
                                showAlert(data.message, 'danger'); // custom alert function
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        });
                });
            });
        }

        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} shadow rounded mb-2 fade-out`;

            // Masukkan isi alert + tombol close
            alertDiv.innerHTML = `<span>${message}</span>`;
            alertDiv;

            const container = document.getElementById('floating-alert-container');
            container.appendChild(alertDiv);

            // Fade out mulai di detik ke-4.5
            setTimeout(() => {
                alertDiv.style.opacity = '0';
            }, 1500);

            // Remove dari DOM di detik ke-5
            setTimeout(() => {
                alertDiv.remove();
            }, 2000);
        }

        function loadBrands(keyword = '') {
            fetch(`ajaxTransactionList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    bindDeleteEvents(); // PENTING!
                });
        }

        // Event awal
        document.addEventListener('DOMContentLoaded', () => {
            loadBrands();
        });
    </script>

    <?php if (isset($_SESSION['success_message'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlert(`<?= $_SESSION['success_message'] ?>`, 'success');
            });
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['danger_message'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlert(`<?= $_SESSION['danger_message'] ?>`, 'danger');
            });
        </script>
        <?php unset($_SESSION['danger_message']); ?>
    <?php endif; ?>
</div>