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
</head>
<body>

    <header class="navbar">
    <div class="logo">CRM-ERP</div>
    <nav>
        <ul class="nav-links">
            <li><a href="#dashboard">Dashboard</a></li>
            <li><a href="#customers">Customers</a></li>
            <li><a href="#orders">Orders</a></li>
            <li><a href="#promotions">Promotions</a></li>
            <li><a href="login.php">SignIn</a></li>
            <li><a href="register.php">SignUp</a></li>
            </li>
        </ul>
    </nav>
</header>


    <section id="dashboard" class="section">
        <h1>Welcome to CRM-ERP</h1>
        <p>Manage customers, orders, and promotions seamlessly!</p>
    </section>

    <section id="customers" class="section">
        <h2>Customer List</h2>
        <div id="customerTable"></div>
    </section>

    <section id="orders" class="section">
        <h2>Order Tracking</h2>
        <div id="orderTable"></div>
    </section>

    <section id="promotions" class="section">
        <h2>Promotions</h2>
        <p>Create and manage promotional offers!</p>
    </section>

    <script src="js/script.js"></script>
</body>
</html>
