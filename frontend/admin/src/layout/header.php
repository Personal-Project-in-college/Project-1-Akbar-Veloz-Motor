<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Akbar Veloz Motor</title>

  <!-- Vendor CSS -->
  <link rel="stylesheet" href="assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css">
  <link rel="stylesheet" href="assets/js/select.dataTables.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/favicon.png">
</head>
<!-- JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const selectAll = document.getElementById('select-all');
      const checkboxes = document.querySelectorAll('.select-row');

      if (selectAll) {
        selectAll.addEventListener('change', function () {
          checkboxes.forEach(cb => cb.checked = this.checked);
        });
      }

      const searchInput = document.getElementById("search-input");
      searchInput.addEventListener("input", function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("#productTable tbody tr");

        rows.forEach(row => {
          const vehicleCode = row.cells[1].textContent.toLowerCase();
          const brandModel = row.cells[2].textContent.toLowerCase();
          row.style.display = vehicleCode.includes(filter) || brandModel.includes(filter) ? "" : "none";
        });
      });
    });

    // Pagination
    let currentPage = 1;
    const rowsPerPage = 5;
    const rows = document.querySelectorAll("#productTable tbody tr");
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    const prevButton = document.getElementById("prev");
    const nextButton = document.getElementById("next");

    function updateTable() {
      rows.forEach((row, index) => {
        row.style.display = index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage ? "" : "none";
      });

      document.querySelectorAll("#pagination .page-item").forEach((item, index) => {
        item.classList.remove("active");
        if (index === currentPage) item.classList.add("active");
      });

      prevButton.classList.toggle("disabled", currentPage === 1);
      nextButton.classList.toggle("disabled", currentPage === totalPages);
    }

    prevButton.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        updateTable();
      }
    });

    nextButton.addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        updateTable();
      }
    });

    updateTable();
  </script>
<body>

 <!-- Navbar -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    <h4 class="font-weight-bold mb-0 text-primary">Akbar Veloz Motor</h4>
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
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="mdi mdi-bell-outline" style="font-size: 20px; vertical-align: middle;"></i>
          <span class="count"></span>
        </a>
      </li>

      <!-- Settings -->
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="mdi mdi-brightness-7" style="font-size: 20px; vertical-align: middle;"></i>
        </a>
      </li>

      <!-- Profile -->
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
          <img src="assets/images/jamal.png" alt="profile" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item">
            <i class="ti-settings text-primary"></i> Settings
          </a>
          <a class="dropdown-item">
            <i class="ti-power-off text-primary"></i> Logout
          </a>
        </div>
      </li>

      <!-- Optional Button -->
      <li class="nav-item nav-settings d-none d-lg-flex">
        <a class="nav-link" href="#">
          <i class="icon-ellipsis"></i>
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
