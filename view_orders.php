<?php
session_start();
require 'php/db_connect.php';

$user_id = $_SESSION['user_id'];

// Handle deleting an item from the cart
if (isset($_POST['delete_item_id'])) {
    $delete_item_id = $_POST['delete_item_id'];

    $delete_query = "DELETE FROM cart WHERE user_id = $user_id AND menu_item_id = $delete_item_id LIMIT 1";

    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['delete_success'] = "Item deleted successfully!";
    } else {
        $_SESSION['delete_error'] = "Error deleting item.";
    }
}

// Handle quantity update
if (isset($_POST['update_quantity']) && isset($_POST['update_item_id'])) {
    $update_quantity = $_POST['update_quantity'];
    $update_item_id = $_POST['update_item_id'];

    if ($update_quantity > 0) {
        $update_query = "UPDATE cart SET quantity = $update_quantity WHERE user_id = $user_id AND menu_item_id = $update_item_id";

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['update_success'] = "Quantity updated successfully!";
        } else {
            $_SESSION['update_error'] = "Error updating quantity.";
        }
    } else {
        $_SESSION['update_error'] = "Invalid quantity.";
    }
}

// Fetch the cart items with the latest order status (if any)
$query = "
    SELECT 
        c.*, 
        m.name, 
        m.price, 
        o.order_status, 
        o.ordered_at 
    FROM cart c
    JOIN menu_items m ON c.menu_item_id = m.id
    LEFT JOIN (
        SELECT * FROM orders 
        WHERE user_id = $user_id 
        ORDER BY ordered_at DESC
    ) o ON o.menu_item_id = c.menu_item_id
    WHERE c.user_id = $user_id
    GROUP BY c.menu_item_id
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="#">🍽️ CRM-ERP Restaurant</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="homepage.php">Home</a></li>
            <li class="nav-item"><a class="nav-link active" href="view_menu.php">View Menu</a></li>
            <li class="nav-item"><a class="nav-link" href="view_orders.php">View Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="feedback.php">Add Feedback</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout 🔒</a></li>
        </ul>
    </div>
</nav>

<div class="container py-5">
    <h2 class="mb-4">Your Cart</h2>

    <?php if (isset($_SESSION['delete_success'])): ?>
        <div class="alert alert-success text-center"><?php echo $_SESSION['delete_success']; ?></div>
        <?php unset($_SESSION['delete_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['delete_error'])): ?>
        <div class="alert alert-danger text-center"><?php echo $_SESSION['delete_error']; ?></div>
        <?php unset($_SESSION['delete_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['update_success'])): ?>
        <div class="alert alert-success text-center"><?php echo $_SESSION['update_success']; ?></div>
        <?php unset($_SESSION['update_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['update_error'])): ?>
        <div class="alert alert-danger text-center"><?php echo $_SESSION['update_error']; ?></div>
        <?php unset($_SESSION['update_error']); ?>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Item Name</th>
                <th>Price (₱)</th>
                <th>Quantity</th>
                <th>Total (₱)</th>
                <th>Status</th>
                <th>Time Limit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $grand_total = 0; ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <form method="post" action="view_orders.php" class="d-inline">
                            <input type="number" name="update_quantity" value="<?php echo $row['quantity']; ?>" min="1" class="form-control w-50 d-inline">
                            <input type="hidden" name="update_item_id" value="<?php echo $row['menu_item_id']; ?>">
                            <button type="submit" class="btn btn-warning btn-sm">Update</button>
                        </form>
                    </td>
                    <td>
                        <?php 
                            $total = $row['price'] * $row['quantity']; 
                            $grand_total += $total;
                            echo number_format($total, 2);
                        ?>
                    </td>
                    <td><?php echo $row['order_status'] ? ucfirst($row['order_status']) : 'Not Ordered'; ?></td>
                    <td>
                        <?php
                            if ($row['ordered_at']) {
                                $order_time = new DateTime($row['ordered_at']);
                                $current_time = new DateTime();
                                $interval = $current_time->diff($order_time);
                                $time_limit = $interval->format('%h hours %i minutes');
                                echo ($row['order_status'] == 'pending') ? $time_limit : 'Order Complete';
                            } else {
                                echo 'Not Ordered';
                            }
                        ?>
                    </td>
                    <td>
                        <form method="post" action="view_orders.php" class="d-inline">
                            <input type="hidden" name="delete_item_id" value="<?php echo $row['menu_item_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Grand Total:</td>
                <td>₱<?php echo number_format($grand_total, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <form method="post" action="checkout.php">
            <button type="submit" class="btn btn-success mt-3">Checkout 🧾</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
