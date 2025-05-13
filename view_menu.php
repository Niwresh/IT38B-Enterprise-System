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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background: #d8a7d8;
            color: #fff;
            display: flex;
            justify-content: space-between;
            padding: 1rem 2rem;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .navbar-nav .nav-link {
            color: white !important; /* Enforcing the white color */
            font-weight: bold;
            transition: 0.3s;
        }

        .navbar .navbar-nav .nav-link:hover {
            text-decoration: underline !important; /* Enforcing the underline on hover */
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Section styling */
        .section {
            padding: 4rem 2rem;
            min-height: 100vh;
        }

        /* Card */
        .card {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
        }

        /* Button */
        .btn {
            background: #00B1FD;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 30px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 1.5rem;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #e64a19;
        }

        /* Animation */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive nav */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-3">
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
                    <img src="<?php echo $row['image_url']; ?>" class="card-img-top" alt="Menu Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                        <p class="card-text fw-bold">₱<?php echo number_format($row['price'], 2); ?></p>
                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="menu_item_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                            <button type="submit" class="btn w-100">Add to Cart 🛒</button>
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
