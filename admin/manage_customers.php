<?php
session_start();
require 'php/db_connect.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get only users with the role 'user' (excluding 'admin' and 'staff')
$users_query = mysqli_query($conn, "SELECT id, username, firstname, fullname, email, role, created_at FROM users WHERE role = 'user'");

// Handle the deletion of a user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the user from the database
    $delete_query = "DELETE FROM users WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: manage_customers.php"); // Redirect back to the manage page
        exit();
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers - CRM-ERP</title>
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
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h5 class="text-center">NAVIGATION</h5>
        <a href="homepage.php" class="active">🏠 Home</a>
        <a href="admin_reports.php">📊 Reports</a>
        <a href="manage_customers.php">👥 Customers</a>
        <a href="inventory.php">📦 Inventory</a>
        <a href="feedback.php">🗨️ Feedback</a>
        <a href="logout.php" class="text-danger">🔒 Logout</a>
    </div>

    <!-- Main content -->
    <div class="flex-grow-1 p-4">
        <h3>MANAGE CUSTOMERS</h3>
        <hr>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users_query)): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['firstname'] ?></td>
                        <td><?= $user['fullname'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td><?= $user['created_at'] ?></td>
                        <td>
                            <!-- Delete button with confirmation -->
                            <a href="manage_customers.php?delete_id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <footer class="text-center mt-5 mb-3">
            <small>&copy; <?= date("Y") ?> CRM-ERP Restaurant System - Admin Panel</small>
        </footer>
    </div>
</div>

</body>
</html>
