<?php
session_start();
require 'php/db_connect.php';

// Redirect if not logged in or not a staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

// Total Orders
$order_total_query = mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM orders");
$order_total_data = mysqli_fetch_assoc($order_total_query);
$total_orders = $order_total_data['total_orders'];

// Orders by Status
$order_status_query = mysqli_query($conn, "
    SELECT order_status, COUNT(*) AS count 
    FROM orders 
    GROUP BY order_status
");

$order_statuses = [];
while ($row = mysqli_fetch_assoc($order_status_query)) {
    $order_statuses[$row['order_status']] = $row['count'];
}

// Orders Per Day (last 7 days)
$order_per_day_query = mysqli_query($conn, "
    SELECT DATE(ordered_at) AS order_date, COUNT(*) AS count 
    FROM orders 
    WHERE ordered_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(ordered_at)
    ORDER BY order_date ASC
");

$order_dates = [];
$order_counts = [];
while ($row = mysqli_fetch_assoc($order_per_day_query)) {
    $order_dates[] = $row['order_date'];
    $order_counts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Waiter Dashboard - CRM-ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="homepage.php" class="active">🏠 Home</a>
        <a href="order.php">📦 Orders</a>
        <a href="menu.php">🍽️ Menu</a>
        <a href="feedback.php">💬 Feedback</a>
        <a href="logout.php" class="text-danger">🔒 Logout</a>
    </div>

    <!-- Main content -->
    <div class="flex-grow-1 p-4">
        <h3>WELCOME WAITER, HERE'S YOUR DASHBOARD</h3>
        <hr>

        <h4 class="mt-4">ORDER STATISTICS:</h4>
        <div class="row my-4">
            <div class="col-md-6">
                <div class="card text-center bg-light border">
                    <div class="card-body">
                        <h5>TOTAL ORDERS:</h5>
                        <p class="display-6"><?= $total_orders ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <?php foreach ($order_statuses as $status => $count): ?>
                    <div class="card text-center bg-light border mb-2">
                        <div class="card-body">
                            <h5><?= strtoupper($status) ?> ORDERS:</h5>
                            <p class="h4"><?= $count ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Orders Per Day (Last 7 Days)</h5>
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Status Distribution</h5>
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center mt-5 mb-3">
            <small>&copy; <?= date("Y") ?> CRM-ERP Restaurant System - Staff Panel</small>
        </footer>
    </div>
</div>

<!-- Chart.js Scripts -->
<script>
    // Line Chart (Orders per Day)
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($order_dates) ?>,
            datasets: [{
                label: 'Orders',
                data: <?= json_encode($order_counts) ?>,
                borderColor: '#34a853',
                fill: false
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Pie Chart (Orders by Status)
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($order_statuses)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($order_statuses)) ?>,
                backgroundColor: ['#f4c20d', '#34a853', '#4285f4', '#db4437', '#ab47bc']
            }]
        }
    });
</script>

</body>
</html>
