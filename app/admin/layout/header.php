<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Akbar Veloz Motor</title>

  <!-- Vendor CSS -->
  <link rel="stylesheet" href="../assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="../assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css">
  <link rel="stylesheet" href="../assets/js/select.dataTables.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="shortcut icon" href="../assets/images/favicon.png">

</head>
<body>

 <!-- Navbar -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <a class="navbar-brand brand-logo me-5" href="index.php"><img src="../assets/images/logo.png" class="me-2" alt="logo" style="width:300px; height: 100%;"/></a>
    <a class="navbar-brand brand-logo-mini" href="index.php"><img src="../assets/images/logo.png" alt="logo" style="width:500px; height:100%;"/></a> 
  </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    
    <!-- Sidebar Toggle -->
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="icon-menu"></span>
    </button>

    <!-- Welcome Message -->
    <ul class="navbar-nav mr-lg-2">
      <li class="nav-item nav-search d-none d-lg-block">
        <div class="input-group">
          <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
            <span class="input-group-text" id="search"></span>
          </div>
          <div>
            <h5 class="font-weight-bold mb-0 text-primary">Welcome!</h5>
          </div>
        </div>
      </li>
    </ul>

    <!-- Navbar Right -->
    <ul class="navbar-nav navbar-nav-right">

     <!-- Notifications -->
<li class="nav-item dropdown">
  <a class="nav-link" href="#" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="mdi mdi-bell-outline" style="font-size: 20px; vertical-align: middle;"></i>
    <span class="badge bg-danger count">3</span>
  </a>
  <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="notificationDropdown">
    <h6 class="dropdown-header">Notifications</h6>
    <a class="dropdown-item">
      <i class="mdi mdi-email-outline text-primary"></i> General
    </a>
    <a class="dropdown-item">
      <i class="mdi mdi-account-outline text-primary"></i> System Alert
    </a>
    <a class="dropdown-item">
      <i class="mdi mdi-alert-circle-outline text-primary"></i> Transaction
      <span class="badge bg-danger count">3</span>
    </a>
  </div>
</li>

      <!-- Profile -->
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
          <img src="../assets/images/jamal.png" alt="profile" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
        <a class="dropdown-item" href="settings.php">
          <i class="ti-settings text-primary"></i> Settings
        </a>
        <a class="dropdown-item" href="logout.php">
          <i class="ti-power-off text-primary"></i> Logout
        </a>

      </li>

    </ul>

    <!-- Offcanvas Toggle (Mobile) -->
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="icon-menu"></span>
    </button>

  </div>
</nav>

<!-- Mulai Container -->
<div class="container-fluid page-body-wrapper">
