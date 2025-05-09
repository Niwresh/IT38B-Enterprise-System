<?php
session_start();
require 'php/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$menu_item_id = (int)$_POST['menu_item_id'];
$quantity = (int)$_POST['quantity'];

// Get current stock
$stock_result = mysqli_query($conn, "SELECT stock_quantity FROM menu_items WHERE id = $menu_item_id");
$stock_row = mysqli_fetch_assoc($stock_result);

if (!$stock_row) {
    $_SESSION['error'] = "Item not found.";
    header("Location: view_menu.php");
    exit();
}

$current_stock = $stock_row['stock_quantity'];

if ($quantity > $current_stock) {
    $_SESSION['error'] = "Not enough stock available. Only $current_stock left.";
    header("Location: view_menu.php");
    exit();
}

// Add to cart (or adjust depending on your schema)
mysqli_query($conn, "INSERT INTO cart (user_id, menu_item_id, quantity) VALUES ($user_id, $menu_item_id, $quantity)");

// Deduct from inventory
mysqli_query($conn, "UPDATE menu_items SET stock_quantity = stock_quantity - $quantity WHERE id = $menu_item_id");

// Optional: If stock reaches 0, mark as unavailable
mysqli_query($conn, "UPDATE menu_items SET available = 0 WHERE id = $menu_item_id AND stock_quantity = 0");

$_SESSION['success'] = "Item added to cart!";
header("Location: view_menu.php");
exit();
?>
