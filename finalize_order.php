<?php
session_start();
require 'php/db_connect.php';

$user_id = $_SESSION['user_id'];

if (!isset($_POST['items']) || empty($_POST['items'])) {
    die("No items to order.");
}

$items = $_POST['items'];
$ordered_at = date('Y-m-d H:i:s');

foreach ($items as $menu_item_id) {
    // Fetch quantity from cart
    $result = mysqli_query($conn, "SELECT quantity FROM cart WHERE user_id = $user_id AND menu_item_id = $menu_item_id");
    $row = mysqli_fetch_assoc($result);
    $qty = $row['quantity'];

    // Insert into orders
    $insert = "INSERT INTO orders (user_id, menu_item_id, quantity, order_status, ordered_at)
               VALUES ($user_id, $menu_item_id, $qty, 'pending', '$ordered_at')";
    mysqli_query($conn, $insert);

    // Optionally remove from cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id AND menu_item_id = $menu_item_id");
}

echo "<h2 style='text-align:center;'>✅ Order Finalized!</h2>";
echo "<p style='text-align:center;'>Thank you for your order. You will receive your receipt shortly.</p>";
echo "<div style='text-align:center;'><a href='view_orders.php' class='btn btn-dark'>Back to Orders</a></div>";
