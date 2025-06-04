<!-- Sidebar -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link" href="../dashboard/index.php">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Home</span>
      </a>
    </li>

    <?php
    include '../../../../helpers/functionSidebarActive.php';

    $isBranchActive = isSidebarMenuActive('branch');
    $isVehicleActive = isSidebarMenuActive('vehicles');
    $isManageActive = isSubMenuActive(['branch', 'vehicles']);
    ?>


    

    <!-- Manage Section -->
    <li class="nav-item">
      <a class="nav-link <?= $isManageActive ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#collapseManage" aria-expanded="<?= $isManageActive ? 'true' : 'false' ?>" aria-controls="collapseManage">
      <i class="mdi mdi-clipboard-text menu-icon" <?= $isManageActive ? 'style="color: white;"' : '' ?>></i>
        <span class="menu-title">Kelola</span>
      </a>
      <div class="collapse <?= $isManageActive ? 'show' : '' ?>" id="collapseManage">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link <?= $isBranchActive ? 'active' : '' ?>" href="../branch/branch.php">Cabang</a></li>
          <li class="nav-item"><a class="nav-link <?= $isVehicleActive ? 'active' : '' ?>" href="../vehicles/vehicles.php">Kendaraan</a></li>
        </ul>
      </div>
    </li>


    <!-- Laporan Section -->
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

    <!-- Settings Section -->
    <li class="nav-item">
      <a class="nav-link" href="settings.php">
        <i class="mdi mdi-account menu-icon"></i>
        <span class="menu-title">Settings</span>
      </a>
    </li>

    <!-- Users Section -->
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


</nav>