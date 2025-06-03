<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ./views/pages/admin/auth/login.php');
    exit;
}

$name = $_SESSION['name'];
$role = $_SESSION['role_name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Landing Page</title>
</head>

<body>
    <h1>Login Sebagai <?= $name ?>, Dengan Role <?= $role ?></h1>
    <h4>Authentication</h4>
    <ul>
        <li><a href="./views/pages/admin/auth/logout.php">Log Out</a>
    </ul>
    <hr>
    <h1>Ini Landing Page Website</h1>
    <h4>Route List</h4>
    <ul>
        <li><a href="views/pages/admin/branch/index.php">Branch ✅</a></li>
        <li><a href="views/pages/admin/partner/index.php">Partner ✅</a></li>
        <li><a href="views/pages/admin/vehicles/index.php">Vehicle ✅</a></li>
        <li><a href="views/pages/admin/vehicle_documents/index.php">Vehicle Document ✅</a></li>
        <li><a href="views/pages/admin/vehicle_photos/index.php">Vehicle Photo ✅</a></li>
        <li><a href="views/pages/admin/vehicle_loans/index.php">Vehicle Loans ✅</a></li>
        <li><a href="views/pages/admin/role/index.php">Role ✅</a></li>
        <li><a href="views/pages/admin/users/index.php">User ✅</a></li>
    </ul>

    <hr>
    <h1>Logic Transaksi</h1>
    <ul>
        <li><a href="views/pages/admin/orders/index.php">Order 🔃</a></li>
        <li><a href="views/pages/admin/transactions/index.php">Transaksi 🔃</a></li>
    </ul>
    <hr>
</body>

</html>