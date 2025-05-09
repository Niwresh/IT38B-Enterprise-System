<?php
session_start();
require 'php/db_connect.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch orders and join with users and menu items
$query = "
    SELECT 
        o.id,
        u.firstname,
        u.fullname,
        m.name AS menu_item,
        o.quantity,
        o.price,
        o.total_price,
        o.ordered_at,
        o.order_status
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN menu_items m ON o.menu_item_id = m.id
    ORDER BY o.ordered_at DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reports - CRM-ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #d8a7d8;
            min-height: 100vh;
            padding-top: 1rem;
        }
        .sidebar a {
            display: block;
            padding: 1rem;
            color: black;
            text-decoration: none;
            font-weight: bold;
        }
        .sidebar a:hover, .sidebar .active {
            background-color: #fff;
            color: red;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h5 class="text-center">NAVIGATION</h5>
        <a href="homepage.php">🏠 Home</a>
        <a href="admin_reports.php" class="active">📊 Reports</a>
        <a href="manage_customers.php">👥 Customers</a>
        <a href="inventory.php">📦 Inventory</a>
        <a href="feedback.php">🗨️ Feedback</a>
        <a href="logout.php" class="text-danger">🔒 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <h3>System Reports</h3>
        <hr>

        <h5>Orders Overview</h5>
        <table class="table table-bordered table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Menu Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Ordered At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['firstname']) ?> (<?= htmlspecialchars($row['fullname']) ?>)</td>
                        <td><?= htmlspecialchars($row['menu_item']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>₱<?= number_format($row['price'], 2) ?></td>
                        <td>₱<?= number_format($row['total_price'], 2) ?></td>
                        <td><?= $row['ordered_at'] ?></td>
                        <td>
                            <?php if ($row['order_status'] === 'pending') : ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php else : ?>
                                <span class="badge bg-success">Done</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <footer class="text-center mt-5 mb-3">
            <small>&copy; <?= date("Y") ?> CRM-ERP Restaurant System - Admin Reports</small>
        </footer>
    </div>
</div>

</body>
</html>
