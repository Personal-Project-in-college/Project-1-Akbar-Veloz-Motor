
<?php
require '../../../../config/koneksi.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $stmt = $koneksi->prepare("UPDATE orders SET is_read = 1 WHERE id = ?");
  $stmt->execute([$id]);
}
?>
