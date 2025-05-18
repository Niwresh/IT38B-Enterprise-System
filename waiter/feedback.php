<?php
session_start();
require 'php/db_connect.php';

// Redirect if not logged in or not staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
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

// Filter logic
$filter = $_GET['filter'] ?? '';
$value = $_GET['value'] ?? '';

$whereClause = '';
$filterLabel = '';

if ($filter === 'rating' && $value) {
    $rating = (int)$value;
    $whereClause = "WHERE f.rating = $rating";
    $filterLabel = "Showing feedbacks with rating: $rating star(s)";
} elseif ($filter === 'month' && $value) {
    $month = (int)$value;
    $whereClause = "WHERE MONTH(f.created_at) = $month";
    $monthName = date("F", mktime(0, 0, 0, $month, 1));
    $filterLabel = "Showing feedbacks from: $monthName";
}

// Fetch feedbacks
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
    $whereClause
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
        <a href="order.php">📦 Orders</a>
        <a href="menu.php">🍽️ Menu</a>
        <a href="feedback.php" class="active">💬 Feedback</a>
        <a href="logout.php" class="text-danger">🔒 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Customer Feedback</h3>

            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter Feedback
                </button>
                <ul class="dropdown-menu" style="min-width: 200px;">
                    <!-- Filter by Rating -->
                    <li>
                        <a class="dropdown-item" href="#" onclick="toggleSubmenu('ratingSubmenu')">⭐ Rating ▸</a>
                        <ul class="list-unstyled ms-3 collapse" id="ratingSubmenu">
                            <?php for ($r = 1; $r <= 5; $r++): ?>
                                <li><a class="dropdown-item" href="?filter=rating&value=<?= $r ?>"><?= $r ?> star(s)</a></li>
                            <?php endfor; ?>
                        </ul>
                    </li>

                    <!-- Filter by Month -->
                    <li>
                        <a class="dropdown-item" href="#" onclick="toggleSubmenu('monthSubmenu')">🗓️ Month ▸</a>
                        <ul class="list-unstyled ms-3 collapse" id="monthSubmenu">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <li><a class="dropdown-item" href="?filter=month&value=<?= $i ?>"><?= date("F", mktime(0, 0, 0, $i, 1)) ?></a></li>
                            <?php endfor; ?>
                        </ul>
                    </li>

                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="feedback.php">Clear Filter</a></li>
                </ul>
            </div>
        </div>

        <?php if ($filterLabel): ?>
            <div class="alert alert-info"><?= $filterLabel ?></div>
        <?php endif; ?>

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
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['fullname']) ?></td>
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
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No feedback found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <footer class="text-center mt-5 mb-3">
            <small>&copy; <?= date("Y") ?> CRM-ERP Restaurant System - Feedback</small>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Dropdown submenu toggling -->
<script>
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id);
        const isOpen = submenu.classList.contains('show');
        document.querySelectorAll('.dropdown-menu .collapse').forEach(el => el.classList.remove('show'));
        if (!isOpen) submenu.classList.add('show');
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu .collapse').forEach(el => el.classList.remove('show'));
        }
    });
</script>

</body>
</html>
