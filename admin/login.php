<?php
// Include database connection
include 'php/db_connect.php';
session_start();

// Initialize message
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check password
        if (password_verify($password, $user['password'])) {
            // Check role
            if ($user['role'] === 'admin') {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header("Location: homepage.php");
                exit();
            } else {
                // Not an admin
                $_SESSION['message'] = "You are not allowed here.";
            }
        } else {
            // Wrong password
            $_SESSION['message'] = "Incorrect password.";
        }
    } else {
        // No user found
        $_SESSION['message'] = "Account not registered. REGISTER FIRST";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CRM-ERP Admin</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
        }
        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
        .modal button {
            background-color: #00B1FD;
            color: white;
            padding: 10px 20px;
            border: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Sign In (Admin Access Only)</h2>
    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</div>

<!-- Modal -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"><?php echo htmlspecialchars($message); ?></p>
        <?php if ($message === "Account not registered. REGISTER FIRST"): ?>
            <a href="register.php"><button>Register</button></a>
        <?php endif; ?>
    </div>
</div>

<script>
    var modal = document.getElementById("errorModal");
    var message = "<?php echo $message; ?>";

    if (message) {
        modal.style.display = "block";
    }

    document.getElementsByClassName("close")[0].onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>