<?php
session_start();
include '../../../../config/koneksi.php';

// Proses login saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $data = $koneksi->prepare("SELECT users.*, roles.name AS role_name FROM users JOIN roles ON users.role_id = roles.id  WHERE users.username = ? AND users.deleted_at IS NULL");
    $data->execute([$username]);
    $user = $data->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['name']      = $user['name'];
        $_SESSION['role_name'] = $user['role_name'];

        header('Location: ../../../../../../index.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!-- HTML Form -->
<h2>Login Admin</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>