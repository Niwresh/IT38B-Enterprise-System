<?php
include 'php/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN CRM-ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">CRM-ERP</div>
        <nav>
            <ul class="nav-links">
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="login.php">SignIn</a></li>
                <li><a href="register.php">SignUp</a></li>
            </ul>
        </nav>
    </header>

    <!-- Single Card Dashboard Section -->
    <section id="dashboard" class="section">
        <div class="card">
            <h1>Welcome to ADMIN CRM-ERP</h1>
            <p>Manage customers, orders, and promotions seamlessly!</p>

            <!-- System Overview -->
            <h2>System Overview</h2>
            <div class="card-container">
                <div class="card-item">
                    <h3>1,200+</h3>
                    <p>Registered Customers</p>
                </div>
                <div class="card-item">
                    <h3>3,500+</h3>
                    <p>Orders Processed</p>
                </div>
                <div class="card-item">
                    <h3>120+</h3>
                    <p>Menu Items</p>
                </div>
                <div class="card-item">
                    <h3>4.7/5</h3>
                    <p>Customer Feedback Rating</p>
                </div>
            </div>

            <!-- Features Section -->
            <h2>Features You’ll Love</h2>
            <ul>
                <li><strong>Customer Management:</strong> View and analyze your customer base.</li>
                <li><strong>Order Control:</strong> Manage and track every order efficiently.</li>
                <li><strong>Promotions Hub:</strong> Launch offers and monitor engagement.</li>
                <li><strong>Reports:</strong> Generate real-time reports to guide business decisions.</li>
            </ul>

            <!-- Call-to-Action Section -->
            <div style="text-align:center; margin-top: 20px;">
                <h2>Ready to Get Started?</h2>
                <a href="login.php" class="btn">Sign In</a>
                <a href="register.php" class="btn btn-secondary">Sign Up</a>
            </div>
        </div>
    </section>

    <script src="js/script.js"></script>
</body>
</html>
