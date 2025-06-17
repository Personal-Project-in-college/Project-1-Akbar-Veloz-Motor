<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['customer_id']) && $_SESSION['customer_id'] > 0;
?>

<header>
  <div class="container">
    <span translate="no"><img src="./assets/icons/logo.png" alt="Akbar Veloz Motor Logo" class="logo"></span>
    <div class="hamburger-menu" id="hamburgerMenu" aria-label="Toggle navigation menu">
      <div class="bar"></div>
      <div class="bar"></div>
      <div class="bar"></div>
    </div>
    <nav aria-label="Main navigation">
      <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        <li>
          <a href="wishlist.php">Simpan
            <span class="wishlist-icon">
              <svg width="20" height="20" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </span>
          </a>
        </li>
        <li><a href="contact-us.php" >Hubungi kami</a></li>
        <li>
          <?php if ($is_logged_in): ?>
            <a href="logout.php" id="logoutLink" class="auth-link">Logout
              <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
              </span>
            </a>
          <?php else: ?>
            <a href="login.php" class="auth-link">Login
              <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-in"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
              </span>
            </a>
          <?php endif; ?>
        </li>
      </ul>
    </nav>
  </div>
</header>
<div class="menu-overlay" id="menuOverlay"></div>