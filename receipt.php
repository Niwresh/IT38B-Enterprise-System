<?php
require 'php/db_connect.php';

if (!isset($_GET['receipt'])) {
    die("Invalid request.");
}

$receipt_code = mysqli_real_escape_string($conn, $_GET['receipt']);

// Fetch all order items for this receipt code
$query = mysqli_query($conn, "
    SELECT 
        o.id AS order_id,
        o.ordered_at,
        o.receipt,
        u.firstname,
        u.fullname,
        u.Email,
        mi.name AS item_name,
        o.quantity,
        o.price
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN menu_items mi ON o.menu_item_id = mi.id
    WHERE o.receipt = '$receipt_code'
");

$orders = [];
while ($row = mysqli_fetch_assoc($query)) {
    $orders[] = $row;
}

if (empty($orders)) {
    die("Receipt not found.");
}

// Use the first row for common info
$main = $orders[0];

// Calculate grand total manually
$grandTotal = 0;
foreach ($orders as $order) {
    $grandTotal += $order['price'] * $order['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt #<?= htmlspecialchars($main['receipt']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .no-print { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>🧾 Order Receipt</h2>
    <p><strong>Receipt #:</strong> <?= htmlspecialchars($main['receipt']) ?></p>
    <p><strong>Order #:</strong> <?= $main['order_id'] ?></p>
    <p><strong>Date:</strong> <?= $main['ordered_at'] ?></p>

    <hr>

    <h5>Customer Info</h5>
    <p><strong>Name:</strong> <?= htmlspecialchars($main['firstname'] . ' ' . $main['fullname']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($main['Email']) ?></p>

    <hr>

    <h5>Order Details</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Menu Item</th>
                <th>Quantity</th>
                <th>Price Each</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['item_name']) ?></td>
                <td><?= $order['quantity'] ?></td>
                <td>₱<?= number_format($order['price'], 2) ?></td>
                <td>₱<?= number_format($order['price'] * $order['quantity'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4 class="text-end">Total: ₱<?= number_format($grandTotal, 2) ?></h4>

    <div class="mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success">🖨️ Print</button>
        <a href="view_orders.php" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
