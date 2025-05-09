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

// Fetch cart items with menu item info
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

    <?php foreach (['delete_success', 'delete_error', 'update_success', 'update_error'] as $msg): ?>
        <?php if (isset($_SESSION[$msg])): ?>
            <div class="alert alert-<?= str_contains($msg, 'error') ? 'danger' : 'success' ?> text-center">
                <?= $_SESSION[$msg]; unset($_SESSION[$msg]); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

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
            <?php mysqli_data_seek($result, 0); ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td>
                        <form method="post" action="view_orders.php" class="d-inline">
                            <input type="number" name="update_quantity" value="<?= $row['quantity'] ?>" min="1" class="form-control w-50 d-inline">
                            <input type="hidden" name="update_item_id" value="<?= $row['menu_item_id'] ?>">
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
                    <td><?= $row['order_status'] ? ucfirst($row['order_status']) : 'Not Ordered' ?></td>
                    <td>
                        <?php
                            if ($row['ordered_at']) {
                                $order_time = new DateTime($row['ordered_at']);
                                $current_time = new DateTime();
                                $interval = $current_time->diff($order_time);
                                echo ($row['order_status'] == 'pending') ? $interval->format('%h hrs %i mins') : 'Order Complete';
                            } else {
                                echo 'Not Ordered';
                            }
                        ?>
                    </td>
                    <td>
                        <form method="post" action="view_orders.php" class="d-inline">
                            <input type="hidden" name="delete_item_id" value="<?= $row['menu_item_id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Grand Total:</td>
                <td colspan="4">₱<?= number_format($grand_total, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#checkoutModal">
            Checkout 🧾
        </button>
    <?php endif; ?>
</div>

<!-- Checkout Summary Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" action="finalize_order.php">
        <div class="modal-header">
          <h5 class="modal-title" id="checkoutModalLabel">Order Summary</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table class="table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              mysqli_data_seek($result, 0); // Reset result pointer
              $grand_total = 0;
              while ($row = mysqli_fetch_assoc($result)):
                  $subtotal = $row['price'] * $row['quantity'];
                  $grand_total += $subtotal;
              ?>
              <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td>₱<?= number_format($subtotal, 2) ?></td>
              </tr>
              <input type="hidden" name="items[]" value="<?= $row['menu_item_id'] ?>">
              <?php endwhile; ?>
              <tr class="fw-bold">
                <td colspan="3" class="text-end">Total</td>
                <td>₱<?= number_format($grand_total, 2) ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Finalize Order ✅</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
