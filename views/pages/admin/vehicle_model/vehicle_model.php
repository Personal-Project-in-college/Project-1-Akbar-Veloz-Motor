<?php


include '../../../../config/koneksi.php';

// 2. Memeriksa apakah pengguna sudah login. Jika belum, akan diarahkan ke halaman login.
include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';

// 3. Mengimpor fungsi untuk menampilkan notifikasi/alert (misal: setelah berhasil menambah data).
include '../../../../helpers/functionShowAlert.php';

// 4. Mengimpor bagian layout header (termasuk tag <head>, CSS, dan bagian atas halaman).
include '../layout/header.php';

// 5. Mengimpor bagian layout sidebar (menu navigasi samping).
include '../layout/sidebar.php';

// Variabel untuk menandai halaman aktif di menu navigasi.
// Mengambil nama file saat ini (misal: "branch.php").
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

        <h3 class="mb-4">Data Kendaraan Model</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <?php if (hasAnyRole(['Owner'])) : ?>
                <a href="create.php" class="btn btn-primary">Tambah</a>
            <?php endif ?>

            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari Model (Merek, Nama)...">
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'vehicle_model.php') ? 'active' : '' ?>" href="vehicle_model.php">Data Aktif</a>
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
                                    <th>Merek</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="vehicleModelTableBody">
                                <tr>
                                    <td colspan="4" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <?php
        // Mengimpor bagian layout footer (termasuk tag penutup, script JS, dll).
        include '../layout/footer.php';
        ?>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('vehicleModelTableBody');

        function loadVehicleModels(keyword = '') {
            fetch(`ajaxVehicleModelList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                });
        }

        // Load pertama
        loadVehicleModels();

        // Saat ketik di search
        searchInput.addEventListener('keyup', function() {
            loadVehicleModels(this.value);
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
                                loadVehicleModels(); // reload tabel
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

        function loadVehicleModels(keyword = '') {
            fetch(`ajaxVehicleModelList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    bindDeleteEvents(); // PENTING!
                });
        }

        // Event awal
        document.addEventListener('DOMContentLoaded', () => {
            loadVehicleModels();

            searchInput.addEventListener('keyup', function() {
                loadVehicleModels(this.value);
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