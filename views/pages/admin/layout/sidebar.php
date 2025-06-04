<?php

/**
 * File: sidebar.php
 * Berisi struktur HTML dan logika PHP untuk navigasi sidebar.
 * Menggunakan helper untuk menandai menu/submenu yang aktif secara dinamis.
 */

// Mengimpor dan menggunakan fungsi helper untuk status aktif menu.
include '../../../../helpers/functionSidebarActive.php';
$isBranchActive = isSidebarMenuActive('branch');
$isVehicleActive = isSidebarMenuActive('vehicles');
$isManageActive = isSubMenuActive(['branch', 'vehicles']);
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    <li class="nav-item">
      <a class="nav-link" href="../dashboard/index.php">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Home</span>
      </a>
    </li>

    <style>
      /* Untuk animasi ikon panah di sidebar */
      .rotate-icon {
        transition: transform 0.3s ease;
      }

      .rotate-icon.rotate {
        transform: rotate(180deg);
      }
    </style>

    <li class="nav-item">
      <a class="nav-link d-flex justify-content-between align-items-center <?= $isManageActive ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#collapseManage" aria-expanded="<?= $isManageActive ? 'true' : 'false' ?>" aria-controls="collapseManage">
        <div class="d-flex align-items-center gap-2">
          <i class="mdi mdi-clipboard-text menu-icon"></i>
          <span class="menu-title">Kelola</span>
        </div>
        <i class="mdi mdi-arrow-down-drop-circle-outline rotate-icon <?= $isManageActive ? 'rotate' : '' ?>"></i>
      </a>
      <div class="collapse <?= $isManageActive ? 'show' : '' ?>" id="collapseManage">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link <?= $isBranchActive ? 'active' : '' ?>" href="../branch/branch.php">Cabang</a></li>
          <li class="nav-item"><a class="nav-link <?= $isVehicleActive ? 'active' : '' ?>" href="../vehicles/vehicles.php">Kendaraan</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#collapseLaporan" aria-expanded="false" aria-controls="collapseLaporan">
        <i class="mdi mdi-file-chart menu-icon"></i>
        <span class="menu-title">Reports</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="collapseLaporan">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="../pages/transactions_reports.php">Transactions</a></li>
          <li class="nav-item"><a class="nav-link" href="../pages/outcome.php">Outcome</a></li>
          <li class="nav-item"><a class="nav-link" href="../pages/report.php">Report</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="settings.php">
        <i class="mdi mdi-account menu-icon"></i>
        <span class="menu-title">Settings</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#collapseUsers" aria-expanded="false" aria-controls="collapseUsers">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Users</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="collapseUsers">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="../pages/users.php">User</a></li>
          <li class="nav-item"><a class="nav-link" href="../pages/partner.php">Partner</a></li>
        </ul>
      </div>
    </li>

  </ul>
</nav>

<script>
  // Menangani klik pada menu 'Kelola' untuk memutar ikon panah
  document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.querySelector('[href="#collapseManage"]');

    if (toggleBtn) {
      const rotateIcon = toggleBtn.querySelector('.rotate-icon');

      toggleBtn.addEventListener('click', function() {
        // Kita tunggu sejenak agar class 'collapsed' diperbarui oleh Bootstrap
        setTimeout(() => {
          if (toggleBtn.classList.contains('collapsed')) {
            rotateIcon.classList.remove('rotate');
          } else {
            rotateIcon.classList.add('rotate');
          }
        }, 10);
      });
    }
  });
</script>