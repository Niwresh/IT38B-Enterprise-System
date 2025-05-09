<?php
session_start();
require 'php/db_connect.php';

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $feedback_id = intval($_GET['delete']);
    $delete_query = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    header("Location: feedback.php");
    exit();
}

// Fetch feedback with user names
$query = "
    SELECT 
        f.id,
        u.firstname,
        u.fullname,
        f.rating,
        f.comment,
        f.created_at
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.id
    ORDER BY f.created_at DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Feedback - CRM-ERP</title>
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
        <a href="homepage.php">🏠 Home</a>
        <a href="admin_reports.php">📊 Reports</a>
        <a href="manage_customers.php">👥 Customers</a>
        <a href="inventory.php">📦 Inventory</a>
        <a href="feedback.php" class="active">🗨️ Feedback</a>
        <a href="logout.php" class="text-danger">🔒 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <h3>Customer Feedback</h3>
        <hr>

        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['firstname'] . ' ' . $row['fullname'] ?></td>
                        <td><?= $row['rating'] ?>/5</td>
                        <td><?= htmlspecialchars($row['comment']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete=<?= $row['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this feedback?');" 
                               class="btn btn-sm btn-danger">
                               🗑️ Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <footer class="text-center mt-5 mb-3">
            <small>&copy; <?= date("Y") ?> CRM-ERP Restaurant System - Feedback</small>
        </footer>
    </div>
</div>

</body>
</html>
