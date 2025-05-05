<?php
session_start();
require 'php/db_connect.php';

$user_id = $_SESSION['user_id'];

// Handle the checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch cart items
    $query = "SELECT c.*, m.name, m.price 
              FROM cart c
              JOIN menu_items m ON c.menu_item_id = m.id
              WHERE c.user_id = $user_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Insert each item from the cart into the orders table
        while ($row = mysqli_fetch_assoc($result)) {
            $menu_item_id = $row['menu_item_id'];
            $quantity = $row['quantity'];
            $price = $row['price'];
            $total = $price * $quantity;
            
            // Insert into orders table
            $order_query = "INSERT INTO orders (user_id, menu_item_id, quantity, total_price, order_status) 
                            VALUES ($user_id, $menu_item_id, $quantity, $total, 'Pending')";
            
            if (!mysqli_query($conn, $order_query)) {
                $_SESSION['checkout_error'] = "Error processing your order.";
                header('Location: view_orders.php');
                exit();
            }
        }

        // If all items inserted successfully, clear the cart
        $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
        if (mysqli_query($conn, $clear_cart_query)) {
            $_SESSION['checkout_success'] = "Order placed successfully!";
        } else {
            $_SESSION['checkout_error'] = "Error clearing cart.";
        }

        header('Location: view_orders.php'); // Redirect to the orders page after checkout
        exit();
    } else {
        $_SESSION['checkout_error'] = "Your cart is empty.";
        header('Location: view_orders.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2>Checkout</h2>

        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['checkout_success'])): ?>
            <div class="alert alert-success text-center">
                <?php echo $_SESSION['checkout_success']; ?>
            </div>
            <?php unset($_SESSION['checkout_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['checkout_error'])): ?>
            <div class="alert alert-danger text-center">
                <?php echo $_SESSION['checkout_error']; ?>
            </div>
            <?php unset($_SESSION['checkout_error']); ?>
        <?php endif; ?>

        <form method="post" action="checkout.php">
            <button type="submit" class="btn btn-success mt-3">Confirm Order 🧾</button>
        </form>
    </div>
</body>
</html>
