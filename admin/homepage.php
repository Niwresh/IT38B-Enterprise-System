<?php
session_start();
require 'php/db_connect.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get total number of users
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$total_users_data = mysqli_fetch_assoc($total_users_query);
$total_users = $total_users_data['total'];

// Get total number of distinct roles (accounts registered)
$total_accounts_query = mysqli_query($conn, "SELECT COUNT(DISTINCT role) AS total_roles FROM users");
$total_accounts_data = mysqli_fetch_assoc($total_accounts_query);
$total_accounts = $total_accounts_data['total_roles'];

// Get user counts per role (admin, user, staff)
$roles = ['admin', 'user', 'staff'];
$role_counts = [];

foreach ($roles as $role) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $role_counts[$role] = (int)$result['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - CRM-ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <h3>WELCOME TO MY ENTERPRISE SYSTEM DASHBOARD</h3>
        <hr>

        <h4 class="mt-4">SYSTEM OVERVIEW:</h4>
        <div class="row my-4">
            <div class="col-md-6">
                <div class="card text-center bg-light border">
                    <div class="card-body">
                        <h5>TOTAL USERS:</h5>
                        <p class="display-6"><?= $total_users ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center bg-light border">
                    <div class="card-body">
                        <h5>TOTAL ACCOUNTS REGISTERED:</h5>
                        <p class="display-6"><?= $total_accounts ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Sales Overview</h5>
                        <canvas id="lineChart"></canvas>

                        <div class="mt-3">
                            <label for="salesView" class="form-label">Select View:</label>
                            <select id="salesView" class="form-select w-auto">
                                <option value="both" selected>SALES</option>
                                <option value="weekly">Weekly Sales</option>
                                <option value="monthly">Monthly Sales</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">User Distribution</h5>
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">UP - COMING NEWS:</h5>
                        <p>New features coming soon...</p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center mt-5 mb-3">
            <small>&copy; <?= date("Y") ?> CRM-ERP Restaurant System - Admin Panel</small>
        </footer>
    </div>
</div>

<!-- Chart.js Script -->
<script>
    const lineCtx = document.getElementById('lineChart').getContext('2d');

    const weeklyLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

    const lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [
                {
                    label: 'Weekly Sales',
                    data: [120, 190, 300, 250, 180, 230, 270],
                    borderColor: '#34a853',
                    backgroundColor: 'rgba(52, 168, 83, 0.2)',
                    tension: 0.3,
                    hidden: false
                },
                {
                    label: 'Monthly Sales',
                    data: [1500, 2300, 1800, 2900, 3100, 2700],
                    borderColor: '#4285f4',
                    backgroundColor: 'rgba(66, 133, 244, 0.2)',
                    tension: 0.3,
                    hidden: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    document.getElementById('salesView').addEventListener('change', function () {
        const selected = this.value;

        if (selected === 'weekly') {
            lineChart.data.labels = weeklyLabels;
            lineChart.data.datasets[0].hidden = false;
            lineChart.data.datasets[1].hidden = true;
        } else if (selected === 'monthly') {
            lineChart.data.labels = monthlyLabels;
            lineChart.data.datasets[0].hidden = true;
            lineChart.data.datasets[1].hidden = false;
        } else {
            // both
            lineChart.data.labels = weeklyLabels;
            lineChart.data.datasets[0].hidden = false;
            lineChart.data.datasets[1].hidden = false;
        }

        lineChart.update();
    });

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Admin', 'User', 'Staff'],
            datasets: [{
                data: [
                    <?= $role_counts['admin'] ?>,
                    <?= $role_counts['user'] ?>,
                    <?= $role_counts['staff'] ?>
                ],
                backgroundColor: ['#f4c20d', '#34a853', '#4285f4']
            }]
        }
    });
</script>

</body>
</html>
