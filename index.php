<?php
// Include the database connection
require_once 'config/database.php';

// Include the model to interact with the database (if necessary)
require_once 'models/customerModel.php';

// Fetch customers (example)
$customers = getCustomers(); // This function is from the customerModel.php, assuming it fetches customers from the database

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Customer Management</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <h1>Customer Relationship Management</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="add_customer.php">Add Customer</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Section -->
    <main>
        <h2>Customer List</h2>

        <!-- Customer Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($customers) > 0): ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></td>
                            <td><?php echo $customer['email']; ?></td>
                            <td><?php echo $customer['phone']; ?></td>
                            <td>
                                <a href="edit_customer.php?id=<?php echo $customer['id']; ?>">Edit</a>
                                <a href="delete_customer.php?id=<?php echo $customer['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No customers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025 CRM System. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
