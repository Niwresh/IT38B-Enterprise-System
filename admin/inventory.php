<?php
session_start();
require 'php/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Add new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];
    $available = $_POST['available'];
    $stock_quantity = (int) $_POST['stock_quantity'];
    $image = mysqli_real_escape_string($conn, $_POST['image_url']);

    if (empty($image)) {
        $image = 'https://via.placeholder.com/60';
    }

    mysqli_query($conn, "INSERT INTO menu_items (name, description, price, available, stock_quantity, image)
        VALUES ('$name', '$desc', $price, $available, $stock_quantity, '$image')");
    header("Location: inventory.php");
    exit();
}

// Edit item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id = $_POST['item_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];
    $available = $_POST['available'];
    $stock_quantity = (int) $_POST['stock_quantity'];
    $image = mysqli_real_escape_string($conn, $_POST['image_url']);

    $image_sql = $image ? ", image_url = '$image'" : "";

    mysqli_query($conn, "UPDATE menu_items SET name='$name', description='$desc', price=$price, available=$available, stock_quantity=$stock_quantity $image_sql WHERE id=$id");
    header("Location: inventory.php");
    exit();
}

// Delete item
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM menu_items WHERE id = $id");
    header("Location: inventory.php");
    exit();
}

// Fetch items
$items_query = mysqli_query($conn, "SELECT * FROM menu_items");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory - CRM-ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3 class="mb-4">Inventory Management</h3>
    <a href="homepage.php" class="btn btn-secondary mb-3 me-2">🏠 Return to Homepage</a>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">➕ Add New Item</button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price (₱)</th>
                <th>Available</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $modals = '';
            while ($row = mysqli_fetch_assoc($items_query)): ?>
                <tr>
                    <td><img src="<?= $row['image_url'] ?: 'https://via.placeholder.com/60' ?>" alt="" width="60" height="60"></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['available'] ? 'Yes' : 'No' ?></td>
                    <td><?= $row['stock_quantity'] ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                        <a href="inventory.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>

                <?php
                $modals .= '
                <div class="modal fade" id="editModal' . $row['id'] . '" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="post" action="inventory.php" class="modal-content">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="item_id" value="' . $row['id'] . '">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Menu Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="' . htmlspecialchars($row['name']) . '" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" required>' . htmlspecialchars($row['description']) . '</textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Price</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="' . $row['price'] . '" required>
                                </div>
                                <div class="mb-3">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="' . $row['stock_quantity'] . '" required>
                                </div>
                                <div class="mb-3">
                                    <label>Image URL</label>
                                    <input type="text" name="image_url" class="form-control" value="' . htmlspecialchars($row['image_url']) . '" placeholder="Enter Image URL">
                                </div>
                                <div class="mb-3">
                                    <label>Available</label>
                                    <select name="available" class="form-control">
                                        <option value="1"' . ($row['available'] ? ' selected' : '') . '>Yes</option>
                                        <option value="0"' . (!$row['available'] ? ' selected' : '') . '>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>';
                ?>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?= $modals ?>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="inventory.php" class="modal-content">
            <input type="hidden" name="action" value="add">
            <div class="modal-header">
                <h5 class="modal-title">Add Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Image URL</label>
                    <input type="text" name="image_url" class="form-control" placeholder="Enter Image URL">
                </div>
                <div class="mb-3">
                    <label>Available</label>
                    <select name="available" class="form-control">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Add Item</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
x