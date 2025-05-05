<?php
session_start();
require 'php/db_connect.php'; // your DB connection

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM menu_items WHERE available = 1";
$result = mysqli_query($conn, $query);

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
    <title>View Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar copied from homepage -->
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
                    <a class="nav-link active" href="view_menu.php">View Menu</a>
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

    <!-- Menu section -->
    <div class="container py-5">
        <h2 class="mb-4">Available Menu</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="Menu Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="card-text"><?php echo $row['description']; ?></p>
                            <p class="card-text fw-bold">₱<?php echo number_format($row['price'], 2); ?></p>
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="menu_item_id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                                <button type="submit" class="btn btn-primary w-100">Add to Cart 🛒</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
