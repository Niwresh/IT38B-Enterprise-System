<?php
include 'php/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM-ERP: Restaurant Management</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="navbar">
    <div class="logo">CRM-ERP</div>
    <nav>
        <ul class="nav-links">
            <li><a href="#dashboard">Dashboard</a></li>
            <li><a href="#About">About</a></li>
            <li><a href="login.php">SignIn</a></li>
            <li><a href="register.php">SignUp</a></li>
        </ul>
    </nav>
</header>

<section id="dashboard" class="section">
    <h1>Welcome to CRM Resto</h1>
    <p>Manage customers, orders, and promotions seamlessly!</p>

    <h2 style="margin-top: 2em;">Popular Dishes</h2>
    <p>Take a sneak peek at some of our best-sellers!</p>

    <div class="menu-grid">
        <div class="menu-item">
            <img src="https://tse1.mm.bing.net/th?id=OIP.sBdsAKI25KJMW8YiY_9Q8QHaE8&pid=Api&P=0&h=220" alt="Cheesy Burger">
            <h3>Cheesy Burger</h3>
            <p>₱150.00 - Juicy grilled beef patty with melted cheese.</p>
        </div>
        <div class="menu-item">
            <img src="https://tse2.mm.bing.net/th?id=OIP.Vmkn2IS83F0hxCul31YXaAHaFj&pid=Api&P=0&h=220" alt="Spaghetti Bolognese">
            <h3>Spaghetti Bolognese</h3>
            <p>₱120.00 - Rich and meaty tomato sauce over spaghetti.</p>
        </div>
        <div class="menu-item">
            <img src="https://tse2.mm.bing.net/th?id=OIP.2l6hKJZbmGhvlQv568z3JAHaE8&pid=Api&P=0&h=220" alt="Classic Milk Tea">
            <h3>Classic Milk Tea</h3>
            <p>₱80.00 - Best-selling cold and creamy milk tea.</p>
        </div>
    </div>

    <a href="register.php" class="btn">Sign up to order now!</a>
</section>
<script src="js/script.js"></script>
</body>
</html>
