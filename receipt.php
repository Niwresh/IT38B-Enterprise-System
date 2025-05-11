<?php
require 'php/db_connect.php';

if (!isset($_GET['receipt'])) {
    die("Invalid request.");
}

$receipt_id = mysqli_real_escape_string($conn, $_GET['receipt']);

$query = mysqli_query($conn, "
    SELECT 
        o.id AS order_id,
        o.total_price,
        o.ordered_at,
        o.receipt,
        u.firstname,
        u.fullname,
        u.Email,
        mi.name AS item_name,
        o.quantity,
        mi.price
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN menu_items mi ON o.menu_item_id = mi.id
    WHERE o.receipt = '$receipt_id'
");

$order = mysqli_fetch_assoc($query);

if (!$order) {
    die("Receipt not found.");
}
?>

<div>
    <h2>🧾 Order Receipt</h2>
    <p><strong>Receipt #:</strong> <?= htmlspecialchars($order['receipt']) ?></p>
    <p><strong>Order #:</strong> <?= $order['order_id'] ?></p>
    <p><strong>Date:</strong> <?= $order['ordered_at'] ?></p>

    <hr>

    <h5>Customer Info</h5>
    <p><strong>Name:</strong> <?= htmlspecialchars($order['firstname'] . ' ' . $order['fullname']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['Email']) ?></p>

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
            <tr>
                <td><?= htmlspecialchars($order['item_name']) ?></td>
                <td><?= $order['quantity'] ?></td>
                <td>₱<?= number_format($order['price'], 2) ?></td>
                <td>₱<?= number_format($order['price'] * $order['quantity'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <h4 class="text-end">Total: ₱<?= number_format($order['total_price'], 2) ?></h4>

    <div class="mt-4 no-print">
        <a href="javascript:window.print()" class="btn btn-success">🖨️ Print</a>
        <a href="view_orders.php" class="btn btn-secondary">← Back to Orders</a>
    </div>
</div>
