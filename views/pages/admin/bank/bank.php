<?php
include '../../../../config/koneksi.php';
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$activePage = basename($_SERVER['PHP_SELF']);
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
        <h3 class="mb-4">Data Bank</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <?php if (hasAnyRole(['Owner'])) : ?>
                <a href="create.php" class="btn btn-primary">Tambah</a>
            <?php endif ?>
            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari Bank (Nama Bank, Nomor, Nama)...">
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'bank.php') ? 'active' : '' ?>" href="bank.php">Data Aktif</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'delete.php') ? 'active' : '' ?>" href="delete.php">Data Terhapus</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body overflow-auto">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bank</th>
                                    <th>Nomor Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="bankTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">Memuat data...</td>
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
        const tableBody = document.getElementById('bankTableBody');

        function loadBanks(keyword = '') {
            fetch(`ajaxBankList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                });
        }

        // Load pertama
        loadBanks();

        // Saat ketik di search
        searchInput.addEventListener('keyup', function() {
            loadBanks(this.value);
        });
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
                                loadBanks(); // reload tabel
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

        function loadBanks(keyword = '') {
            fetch(`ajaxBankList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    bindDeleteEvents(); // PENTING!
                });
        }

        // Event awal
        document.addEventListener('DOMContentLoaded', () => {
            loadBanks();

            searchInput.addEventListener('keyup', function() {
                loadBanks(this.value);
            });
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