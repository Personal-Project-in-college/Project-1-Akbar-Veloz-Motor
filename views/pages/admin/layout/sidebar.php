<?php

/**
 * File: sidebar.php
 * Berisi struktur HTML dan logika PHP untuk navigasi sidebar.
 * Menggunakan helper untuk menandai menu/submenu yang aktif secara dinamis.
 */

// Mengimpor dan menggunakan fungsi helper untuk status aktif menu.
include '../../../../helpers/functionSidebarActive.php';
include_once '../../../../helpers/functionCheckRole.php';

$isBrandActive = isSidebarMenuActive('brand');
$isBranchActive = isSidebarMenuActive('branch');
$isPartnerActive = isSidebarMenuActive('partner');
$isVehicleActive = isSidebarMenuActive('vehicles');
$isVehicleModelActive = isSidebarMenuActive('vehicle_model');
$isVehicleLoanActive = isSidebarMenuActive('vehicle_loans');
$isRoleActive = isSidebarMenuActive('role');
$isUserActive = isSidebarMenuActive('users');

$isManageActive = isSubMenuActive(['brand', 'branch', 'partner', 'vehicles', 'vehicle_model', 'vehicle_loans', 'role', 'users']);

$isOrderActive = isSidebarMenuActive('orders');
$isTransactionsActive = isSubMenuActive(['orders']);


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
      .menu-icon.text-primary {
        color: white !important;
      }
    </style>

    <!-- Manage Section -->
    <li class="nav-item">
      <a class="nav-link <?= $isManageActive ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#collapseManage" aria-expanded="<?= $isManageActive ? 'true' : 'false' ?>" aria-controls="collapseManage">
        <i class="mdi mdi-clipboard-text menu-icon <?= $isManageActive ? 'text-primary' : '' ?>"></i>
        <span class="menu-title">Kelola</span>
      </a>
      <div class="collapse <?= $isManageActive ? 'show' : '' ?>" id="collapseManage">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link <?= $isBranchActive ? 'active' : '' ?>" href="../branch/branch.php">Cabang</a></li>
          <?php if (hasAnyRole(['Owner'])) : ?>
            <li class="nav-item"><a class="nav-link <?= $isRoleActive ? 'active' : '' ?>" href="../role/role.php">Jabatan</a></li>
            <li class="nav-item"><a class="nav-link <?= $isUserActive ? 'active' : '' ?>" href="../users/users.php">Karyawan</a></li>
          <?php endif ?>
          <li class="nav-item"><a class="nav-link <?= $isVehicleActive ? 'active' : '' ?>" href="../vehicles/vehicles.php">Kendaraan</a></li>
          <li class="nav-item"><a class="nav-link <?= $isVehicleModelActive ? 'active' : '' ?>" href="../vehicle_model/vehicle_model.php">Model Kendaraan</a></li>
          <li class="nav-item"><a class="nav-link <?= $isBrandActive ? 'active' : '' ?>" href="../brand/brand.php">Merek</a></li>
          <li class="nav-item"><a class="nav-link <?= $isPartnerActive ? 'active' : '' ?>" href="../partner/partner.php">Partner</a></li>
          <li class="nav-item"><a class="nav-link <?= $isVehicleLoanActive ? 'active' : '' ?>" href="../vehicle_loans/vehicle_loans.php">Peminjaman</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= $isTransactionsActive ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#collapseUsers" aria-expanded="<?= $isTransactionsActive ? 'true' : 'false' ?>" aria-controls="collapseUsers">
        <i class="mdi mdi-cash-register menu-icon"></i>
        <span class="menu-title">Transaksi</span>
      </a>
      <div class="collapse" id="collapseUsers">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link <?= $isOrderActive ? 'active' : '' ?>" href="../orders/orders.php">Pesanan</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= $isReportActive ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#collapseLaporan" aria-expanded="false" aria-controls="collapseLaporan">
        <i class="mdi mdi-file-chart menu-icon"></i>
        <span class="menu-title">Laporan</span>
      </a>
      <div class="collapse <?= $isReportActive ? 'show' : '' ?>" id="collapseLaporan">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="../pages/transactions_reports.php">Kendaraan Masuk</a></li>
          <li class="nav-item"><a class="nav-link" href="../transactions/transactions.php">Penjualan</a></li>
        </ul>
      </div>
    </li>



    <li class="nav-item">
      <a class="nav-link" href="../profile/profile.php">
        <i class="mdi mdi-account menu-icon"></i>
        <span class="menu-title">Profile</span>
      </a>
    </li>


  </ul>
</nav>