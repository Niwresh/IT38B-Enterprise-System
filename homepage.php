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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="#">🍽️ CRM-ERP Restaurant</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <a class="nav-link" href="homepage.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_menu.php">View Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_orders.php">View Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="feedback.php">Add Feedback</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">Logout 🔒</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <p class="text-center text-muted">Use the navigation bar above to manage your orders and feedback.</p>
    </div>

    <footer class="text-center mt-5 mb-3">
        <small>&copy; <?php echo date("Y"); ?> CRM-ERP Restaurant System. All rights reserved.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
