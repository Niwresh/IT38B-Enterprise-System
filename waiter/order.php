<?php
session_start();
require 'php/db_connect.php';

// Redirect if not staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

// Handle update order status
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

    // If marked as done, generate receipt
    if ($new_status === 'done') {
        $receipt = uniqid('RCPT-'); // Example: RCPT-663f25b718cc1
        mysqli_query($conn, "UPDATE orders SET order_status = '$new_status', receipt = '$receipt' WHERE id = $order_id");
    } else {
        // Just update the status
        mysqli_query($conn, "UPDATE orders SET order_status = '$new_status' WHERE id = $order_id");
    }

    header("Location: order.php");
    exit();
}

// Fetch all orders with user info
$result = mysqli_query($conn, "
    SELECT orders.id AS order_id, users.firstname, users.fullname, users.Email, 
           orders.total_price, orders.order_status, orders.ordered_at, orders.receipt 
    FROM orders 
    LEFT JOIN users ON orders.user_id = users.id
    ORDER BY orders.ordered_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders - CRM-ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-status {
            margin-right: 5px;
        }
    </style>
</head>
<body class="p-4">
    <h2 class="mb-4">📦 Orders Management</h2>

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
        </tbody>
    </table>

    <a href="homepage.php" class="btn btn-secondary mt-3">← Back to Home</a>
</body>
</html>
