<?php
// Include database connection
include 'php/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the form input
    $username = trim($_POST["username"]);
    $firstname = trim($_POST["firstname"]);
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];

    // Check if passwords match
    if ($password !== $confirmpassword) {
        die("Error: Passwords do not match. <a href='register.php'>Go back</a>");
    }

    // Check if email or username already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        die("Error: Email or username already exists. <a href='register.php'>Try again</a>");
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, firstname, fullname, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $firstname, $fullname, $email, $hashedPassword);

    if ($stmt->execute()) {
        // Registration successful, show modal and redirect
        echo "
        <script>
            alert('Registration successful! You will be redirected to the login page.');
            window.location.href = 'login.php';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and database connection
    $stmt->close();
    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CRM-ERP</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<div class="form-container">
    <h2>Create Your CRM-ERP Account</h2>
    <form action="register.php" method="POST">
        <input type="text" name="username" placeholder="User Name" required>
        <input type="text" name="firstname" placeholder="First Name" required>
        <input type="text" name="fullname" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirmpassword" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="login.php">Sign in</a></p>
    </form>
</div>

</body>
</html>
