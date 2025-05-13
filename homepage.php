<?php
session_start();
require 'php/db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $count_query = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = $uid";
    $count_result = mysqli_query($conn, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $cart_count = $count_row['total_items'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard - CRM-ERP Restaurant System</title>
    <link rel="stylesheet" href="css/homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Custom Navbar -->
    <header class="navbar">
        <div class="logo">🍽️ CRM-ERP Restaurant</div>
        <ul class="nav-links">
            <li><a href="homepage.php">Home</a></li>
            <li><a href="view_menu.php">View Menu</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
            <li><a href="feedback.php">Add Feedback</a></li>
            <li><a class="text-danger" href="logout.php">Logout 🔒</a></li>
        </ul>
    </header>

    <!-- Main Content -->
    <section class="section">
        <div class="card" style="max-width: 700px; margin: auto; text-align: center;">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p style="color: #666;">You have <strong><?php echo $cart_count; ?></strong> item(s) in your cart.</p>
            <p>Use the navigation links above to manage your orders, browse the menu, or send us your feedback. We're glad to have you!</p>
            <a href="view_menu.php" class="btn">Order Now</a>
        </div>
    </section>

    <footer class="text-center mt-5 mb-3">
        <small>&copy; <?php echo date("Y"); ?> CRM-ERP Restaurant System. All rights reserved.</small>
    </footer>

</body>
</html>
