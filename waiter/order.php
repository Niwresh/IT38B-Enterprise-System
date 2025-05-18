<?php
session_start();
require 'php/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

// Filtering logic
$filter = $_GET['filter'] ?? '';
$value = $_GET['value'] ?? '';

$whereClause = '';
$filterLabel = '';

if ($filter === 'weekly' && $value) {
    // Escape input to prevent SQL injection
    $day = mysqli_real_escape_string($conn, $value);
    $whereClause = "WHERE DAYNAME(orders.ordered_at) = '$day'";
    $filterLabel = "Showing orders for: $day";
} elseif ($filter === 'monthly' && $value) {
    $month = (int)$value;
    $whereClause = "WHERE MONTH(orders.ordered_at) = $month";
    $monthName = date("F", mktime(0, 0, 0, $month, 1));
    $filterLabel = "Showing orders for: $monthName";
}

$query = "
    SELECT orders.id AS order_id, users.firstname, users.fullname, users.Email, 
           orders.total_price, orders.order_status, orders.ordered_at, orders.receipt 
    FROM orders 
    LEFT JOIN users ON orders.user_id = users.id
    $whereClause
    ORDER BY orders.ordered_at DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders - CRM-ERP Restaurant System</title>
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
        /* Align filter form inline */
        .filter-form select {
            max-width: 150px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h5 class="text-center">NAVIGATION</h5>
        <a href="homepage.php">🏠 Home</a>
        <a href="order.php" class="active">📦 Orders</a>
        <a href="menu.php">🍽️ Menu</a>
        <a href="feedback.php">💬 Feedback</a>
        <a href="logout.php" class="text-danger">🔒 Logout</a>
    </div>

    <!-- Main content -->
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📦 Orders Management</h2>

            <!-- Filter Form -->
            <form method="GET" class="filter-form d-flex align-items-center">
                <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Filter By</option>
                    <option value="weekly" <?= ($filter === 'weekly') ? 'selected' : '' ?>>Weekly (Day)</option>
                    <option value="monthly" <?= ($filter === 'monthly') ? 'selected' : '' ?>>Monthly</option>
                </select>

                <?php if ($filter === 'weekly'): ?>
                    <select name="value" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Select Day</option>
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ($days as $day): ?>
                            <option value="<?= $day ?>" <?= ($value === $day) ? 'selected' : '' ?>><?= $day ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($filter === 'monthly'): ?>
                    <select name="value" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Select Month</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= ($value == $i) ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $i, 1)) ?></option>
                        <?php endfor; ?>
                    </select>
                <?php endif; ?>

                <?php if ($filter || $value): ?>
                    <a href="order.php" class="btn btn-sm btn-outline-secondary ms-2">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($filterLabel): ?>
            <div class="alert alert-info"><?= htmlspecialchars($filterLabel) ?></div>
        <?php endif; ?>

        <table class="table table-bordered table-hover">
            <thead class="table-secondary">
                <tr>
                    <th>Order #</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Ordered At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['order_id'] ?></td>
                        <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['Email']) ?></td>
                        <td>₱<?= number_format($row['total_price'], 2) ?></td>
                        <td><span class="badge bg-info"><?= ucfirst($row['order_status']) ?></span></td>
                        <td><?= $row['ordered_at'] ?></td>
                        <td>
                            <form method="POST" class="d-flex mb-1">
                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                <select name="new_status" class="form-select form-select-sm me-2" required>
                                    <option disabled selected>Update...</option>
                                    <option value="done">Done</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-success">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No orders found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
