<?php

/**
 * File: branch.php
 * Halaman ini bertanggung jawab untuk menampilkan daftar data cabang (branches) yang aktif.
 * Fitur:
 * - Menampilkan data dalam tabel dengan pagination.
 * - Tab untuk beralih antara data aktif dan data yang sudah dihapus (soft delete).
 * - Tombol untuk menambah data baru.
 * - Kolom pencarian (hanya tampilan).
 * - Keamanan dengan memeriksa status login pengguna.
 */

// ------------------------------
// INISIALISASI & KONFIGURASI
// ------------------------------

// 1. Mengimpor file koneksi database.
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
    @keyframes slideDown {
        0% {
            transform: translateY(-100px);
            opacity: 0;
        }

        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        transform: translateY(-100px);
        opacity: 0;
    }

    .modal.fade.show .modal-dialog {
        transform: translateY(0);
        opacity: 1;
        animation: slideDown 0.3s ease-out;
    }

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

        <?php
        // Menjalankan fungsi untuk menampilkan alert jika ada.
        showAlert();
        ?>

        <h3 class="mb-4">Data Peminjaman Kendaraan</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <a href="create.php" class="btn btn-primary">Tambah</a>
            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari Peminjam (Kode Kendaraan, Name)...">
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'vehicle_loans.php') ? 'active' : '' ?>" href="vehicle_loans.php">Data Aktif</a>
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
                                    <th>Vehicle ID</th>
                                    <th>Partner</th>
                                    <th>Pinjam</th>
                                    <th>Dikembalikan</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="vehicleLoanTableBody">
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
        const tableBody = document.getElementById('vehicleLoanTableBody');

        function loadVehicleLoans(keyword = '') {
            fetch(`ajaxVehicleLoansList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;

                    document.querySelectorAll('[data-note]').forEach(button => {
                        button.addEventListener('click', function() {
                            const noteText = this.getAttribute('data-note');
                            document.getElementById('noteText').innerText = noteText || 'Tidak ada catatan.';
                        });
                    });
                });
        }

        // Load pertama
        loadVehicleLoans();

        // Saat ketik di search
        searchInput.addEventListener('keyup', function() {
            loadVehicleLoans(this.value);
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
                                loadVehicleLoans(); // reload tabel
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

        function loadVehicleLoans(keyword = '') {
            fetch(`ajaxVehicleLoansList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    bindDeleteEvents(); // PENTING!

                    document.querySelectorAll('[data-note]').forEach(button => {
                        button.addEventListener('click', function() {
                            const noteText = this.getAttribute('data-note');
                            document.getElementById('noteText').innerText = noteText || 'Tidak ada catatan.';
                        });
                    });
                });
        }

        // Event awal
        document.addEventListener('DOMContentLoaded', () => {
            loadVehicleLoans();

            searchInput.addEventListener('keyup', function() {
                loadVehicleLoans(this.value);
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


<!-- Modal Show Note -->
<div class="modal fade" id="modalShowNote" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catatan Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="noteText" class="text-start"></p>
            </div>
        </div>
    </div>
</div>