<!DOCTYPE html>
<html lang="id" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include("./layouts/navbar.php");?>


    <main class="container">
        <section class="wishlist">
            <h2>Kendaraan yang Disukai</h2>
            
            <div class="wishlist-items">
                <!-- Item wishlist akan dimuat secara dinamis oleh JavaScript -->
            </div>
        </section>
    </main>

       <!-- Chat widget -->
    <?php include("./layouts/chat/chat_widget.php");?>
    
    <!-- Footer -->
    <?php include("./layouts/footer.php");?>

    <script src="./js/global.js"></script>
    <script src="./js/wishlist.js"></script>
</body>
</html>