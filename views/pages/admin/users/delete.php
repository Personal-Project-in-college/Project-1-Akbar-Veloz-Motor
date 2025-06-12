<?php

include '../../../../config/koneksi.php';


include '../../../../helpers/functionCheckLogin.php';
checkLogin();
include '../../../../helpers/functionCheckRole.php';


include '../../../../helpers/functionShowAlert.php';


include '../layout/header.php';


include '../layout/sidebar.php';


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

        <h3 class="mb-4">Data Karyawan Terhapus</h3>

        <div class="d-flex align-items-center flex-wrap mb-3 gap-2">
            <?php if (hasAnyRole(['Owner'])) : ?>
                <a href="create.php" class="btn btn-primary">Tambah</a>
            <?php endif ?>
            <div class="flex-grow-1 d-flex align-items-center" style="min-width: 250px;">
                <input type="text" class="form-control rounded-pill" id="search-input" placeholder="Cari">
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'users.php') ? 'active' : '' ?>" href="users.php">Data Aktif</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-primary <?= ($activePage == 'delete.php') ? 'active' : '' ?>" href="delete.php">Data Terhapus</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Telepon</th>
                                    <th>Alamat</th>
                                    <th>Jabatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="deletedUserTableBody">
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
        const tableBody = document.getElementById('deletedUserTableBody');

        function loadDeletedUsers(keyword = '') {
            fetch(`ajaxUserDeletedList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                });
        }

        loadDeletedUsers();

        searchInput.addEventListener('keyup', function() {
            loadDeletedUsers(this.value);
        });
    </script>

    <script>
        function bindRestoreEvents() {
            document.querySelectorAll('.restore-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    fetch('restore.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id=' + encodeURIComponent(id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                loadDeletedUsers(); // reload tabel
                                showAlert(data.message, 'success'); // custom alert function
                            } else {
                                showAlert(data.message, 'success');
                            }
                        });
                });
            });
        }

        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} shadow rounded mb-2 fade-out`;

            // Buat tombol close
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '&times;';
            closeBtn.className = 'close-btn';
            closeBtn.onclick = () => alertDiv.remove();

            // Masukkan isi alert + tombol close
            alertDiv.innerHTML = `<span>${message}</span>`;
            alertDiv.appendChild(closeBtn);

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

        function loadDeletedUsers(keyword = '') {
            fetch(`ajaxUserDeletedList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    bindRestoreEvents(); // PENTING!
                    // bindDestroyEvents(); // PENTING!
                });
        }

        // Event awal
        document.addEventListener('DOMContentLoaded', () => {
            loadDeletedUsers();

            searchInput.addEventListener('keyup', function() {
                loadDeletedUsers(this.value);
            });
        });
    </script>

    <script>
        function bindDestroyEvents() {
            document.querySelectorAll('.destroy-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    fetch('destroy.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id=' + encodeURIComponent(id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                loadDeletedUsers(); // reload tabel
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

            // Buat tombol close
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '&times;';
            closeBtn.className = 'close-btn';
            closeBtn.onclick = () => alertDiv.remove();

            // Masukkan isi alert + tombol close
            alertDiv.innerHTML = `<span>${message}</span>`;
            alertDiv.appendChild(closeBtn);

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

        function loadDeletedUsers(keyword = '') {
            fetch(`ajaxUserDeletedList.php?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    // bindDestroyEvents(); // PENTING!
                    bindRestoreEvents(); // PENTING!
                });
        }

        // Event awal
        document.addEventListener('DOMContentLoaded', () => {
            loadDeletedUsers();

            searchInput.addEventListener('keyup', function() {
                loadDeletedUsers(this.value);
            });
        });
    </script>
</div>