<?php
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

include '../layout/header.php';
include '../layout/sidebar.php';

$activePage = basename($_SERVER['PHP_SELF']);

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
?>

<style>
    .fade-out {
        transition: opacity 0.5s ease-out;
        opacity: 1;
    }

    .alert .close-btn {
        float: right;
        font-size: 1.2rem;
        font-weight: bold;
        line-height: 1;
        color: inherit;
        background: none;
        border: none;
        padding: 0;
        margin-left: 10px;
        cursor: pointer;
    }

    #floating-alert-container .alert {
        animation: slideInLeft 0.3s ease-out;
    }

    @keyframes slideInLeft {
        from {
            transform: translateX(-50px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>

<div id="floating-alert-container" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999; max-width: 300px;"></div>
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="mb-4">Data Transaksi</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <?php if (hasAnyRole(['Owner'])) : ?>
                <a href="create.php" class="btn btn-primary">Tambah</a>
            <?php endif ?>
            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari Transaksi (Customer, Kendaraan)...">
            </div>
        </div>

        <form method="GET" class="mb-3 mt-4 d-flex flex-wrap align-items-end gap-2">
            <div>
                <label>Dari Tanggal:</label>
                <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
            </div>
            <div>
                <label>Sampai Tanggal:</label>
                <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
            </div>
            <div>
                <button type="submit" class="btn btn-dark">Filter</button>
                <a href="transactions_report.php" class="btn btn-secondary text-white">Reset</a>
            </div>
            <div>
                <a href="export_excel.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-success text-white">Export Excel</a>
            </div>
            <div>
                <a href="export_pdf.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-danger text-white">Export PDF</a>
            </div>
        </form>


        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'transactions_report.php') ? 'active' : '' ?>" href="transactions_report.php">Transaksi Selesai</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'delete.php') ? 'active' : '' ?>" href="delete.php">Transaksi Dibatalkan</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal Pesan</th>
                                    <th>Kendaraan</th>
                                    <th>Customer</th>
                                    <th>Total Pembayaran</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="transactionTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../layout/footer.php'; ?>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('transactionTableBody');
        const startDate = "<?= $startDate ?>";
        const endDate = "<?= $endDate ?>";

        function loadTransactions(keyword = '') {
            const params = new URLSearchParams({
                keyword,
                start_date: startDate,
                end_date: endDate
            });
            fetch(`ajaxTransactionList.php?${params.toString()}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    bindDeleteEvents();
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadTransactions();

            searchInput.addEventListener('keyup', function() {
                loadTransactions(this.value);
            });
        });

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
                                loadTransactions();
                                showAlert(data.message, 'danger');
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
            alertDiv.innerHTML = `<span>${message}</span>`;
            const container = document.getElementById('floating-alert-container');
            container.appendChild(alertDiv);
            setTimeout(() => alertDiv.style.opacity = '0', 1500);
            setTimeout(() => alertDiv.remove(), 2000);
        }
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