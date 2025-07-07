<?php

/**
 * File: vehicles.php
 * Halaman utama untuk menampilkan daftar semua kendaraan yang aktif dalam sistem.
 * Dilengkapi dengan fitur pagination, tombol tambah, tab data aktif/terhapus,
 * serta detail seperti STNK dan status kendaraan.
 */

// 1. Inisialisasi dan Konfigurasi Dasar
// Bagian ini mencakup file-file konfigurasi, helper, dan pengecekan sesi login.
include '../../../../config/koneksi.php';              // Menghubungkan ke konfigurasi database.
include '../../../../helpers/functionCheckLogin.php'; // Memuat fungsi untuk memeriksa status login pengguna.
checkLogin();                                         // Menjalankan fungsi untuk memastikan pengguna sudah login sebelum mengakses halaman.
include '../layout/header.php';                       // Memuat bagian header dari tata letak halaman.
include '../layout/sidebar.php';                      // Memuat bagian sidebar dari tata letak halaman.

// Mengambil nama file saat ini dari URL.
// Ini digunakan untuk menandai tab navigasi mana yang sedang aktif di halaman.
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
        <h3 class="mb-4">Data Kendaraan</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <a href="create.php" class="btn btn-primary">Tambah</a>

            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari Kendaraan (Kode, Merek, Model)...">
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'vehicles.php') ? 'active' : '' ?>" href="vehicles.php">Data Aktif</a>
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
                                    <th>Kode</th>
                                    <th>Brand Model</th>
                                    <th>Type Vehicle</th>
                                    <th>STNK Deadline</th>
                                    <th>Price Display</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="vehicleTableBody">
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
        const tableBody = document.getElementById('vehicleTableBody');

        function loadVehicles(keyword = '') {
            fetch(`ajaxVehiclesList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                });
        }

        // Load pertama
        loadVehicles();

        // Saat ketik di search
        searchInput.addEventListener('keyup', function() {
            loadVehicles(this.value);
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
                                loadVehicles(); // reload tabel
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

        function loadVehicles(keyword = '') {
            fetch(`ajaxVehiclesList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    bindDeleteEvents(); // PENTING!
                });
        }

        // Event awal
        document.addEventListener('DOMContentLoaded', () => {
            loadVehicles();

            searchInput.addEventListener('keyup', function() {
                loadVehicles(this.value);
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