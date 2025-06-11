<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<!-- Main Content -->
    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4">System Alert</h3>

       <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'notification_general.php') ? 'active' : '' ?>" href="notification_general.php">Notification General <span class="badge bg-primary">2</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'system_alert.php') ? 'active' : '' ?>" href="system_alert.php">System Alert <span class="badge bg-primary">7</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-primary <?= ($activePage == 'notification_transaction.php') ? 'active' : '' ?>" href="notification_transaction.php">Notification Transaction <span class="badge bg-primary">99+</span></a>
        </ul>



<!-- Plugin js for this page -->
<script src="../assets/vendors/chart.js/chart.umd.js"></script>
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="../assets/js/index.js"></script>
<!-- End custom js for this page-->
 

    <?php include '../layout/footer.php'; ?>