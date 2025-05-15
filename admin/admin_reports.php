<?php
session_start();
require 'php/db_connect.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle filters
$filter_type = $_GET['filter_type'] ?? '';
$day = $_GET['day'] ?? '';
$month = $_GET['month'] ?? '';
$filter_condition = "";

if ($filter_type === 'weekly' && $day !== '') {
    $filter_condition = "WHERE DAYNAME(o.ordered_at) = '" . mysqli_real_escape_string($conn, $day) . "'";
} elseif ($filter_type === 'monthly' && $month !== '') {
    $filter_condition = "WHERE MONTHNAME(o.ordered_at) = '" . mysqli_real_escape_string($conn, $month) . "'";
}

// Fetch orders with optional filter
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
    $filter_condition
    ORDER BY o.ordered_at DESC
";

$result = mysqli_query($conn, $query);

// Calculate metrics
$total_orders = 0;
$pending_orders = 0;
$done_orders = 0;
$cancelled_orders = 0;
$total_revenue = 0.00;

mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
    $total_orders++;
    if ($row['order_status'] === 'pending') {
        $pending_orders++;
    } elseif ($row['order_status'] === 'done') {
        $done_orders++;
        $total_revenue += $row['total_price'];
    } elseif ($row['order_status'] === 'cancelled') {
        $cancelled_orders++;
    }
}

mysqli_data_seek($result, 0); // Reset again for table display
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

        <!-- Summary Cards -->
        <div class="row text-center mb-4">
            <div class="col-md-2"><div class="bg-primary text-white p-3 rounded">Total Orders<br><strong><?= $total_orders ?></strong></div></div>
            <div class="col-md-2"><div class="bg-warning text-dark p-3 rounded">Pending<br><strong><?= $pending_orders ?></strong></div></div>
            <div class="col-md-2"><div class="bg-success text-white p-3 rounded">Completed<br><strong><?= $done_orders ?></strong></div></div>
            <div class="col-md-2"><div class="bg-danger text-white p-3 rounded">Cancelled<br><strong><?= $cancelled_orders ?></strong></div></div>
            <div class="col-md-4"><div class="bg-dark text-white p-3 rounded">Total Revenue<br><strong>₱<?= number_format($total_revenue, 2) ?></strong></div></div>
        </div>

        <!-- Export Button -->
        <form method="POST" action="export_orders.php">
            <button type="submit" class="btn btn-outline-secondary mb-3">⬇️ Export to CSV</button>
        </form>

        <!-- Orders Header and Filter -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>Orders Overview</h5>

            <form method="GET" class="d-flex align-items-center gap-2">
                <select name="filter_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Filter by</option>
                    <option value="weekly" <?= ($filter_type === 'weekly') ? 'selected' : '' ?>>Weekly</option>
                    <option value="monthly" <?= ($filter_type === 'monthly') ? 'selected' : '' ?>>Monthly</option>
                </select>

                <?php if ($filter_type === 'weekly') : ?>
                    <select name="day" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Select Day</option>
                        <?php
                        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        foreach ($days as $d) {
                            $selected = ($day === $d) ? 'selected' : '';
                            echo "<option value='$d' $selected>$d</option>";
                        }
                        ?>
                    </select>
                <?php elseif ($filter_type === 'monthly') : ?>
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Select Month</option>
                        <?php
                        $months = [
                            'January','February','March','April','May','June',
                            'July','August','September','October','November','December'
                        ];
                        foreach ($months as $m) {
                            $selected = ($month === $m) ? 'selected' : '';
                            echo "<option value='$m' $selected>$m</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php if ($filter_type || $day || $month) : ?>
                    <a href="admin_reports.php" class="btn btn-sm btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Orders Table -->
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
                <?php if (mysqli_num_rows($result) > 0): ?>
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
                                <?php elseif ($row['order_status'] === 'done') : ?>
                                    <span class="badge bg-success">Done</span>
                                <?php elseif ($row['order_status'] === 'cancelled') : ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">No orders found for the selected filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <footer class="text-center mt-5 mb-3">
            <small>&copy; <?= date("Y") ?> CRM-ERP Restaurant System - Admin Reports</small>
        </footer>
    </div>
</div>

</body>
</html>
