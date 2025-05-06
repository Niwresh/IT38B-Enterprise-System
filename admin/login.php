<?php
// Include database connection
include 'php/db_connect.php';

// Start session for error/success messages
session_start();

// Check if there's a session message to display (success or error)
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);  // Clear the message after it has been displayed

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the user inputs
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare the query to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session and redirect to homepage
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Redirect to homepage
            header("Location: homepage.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['message'] = "Error: Incorrect password.";
        }
    } else {
        // User not found
        $_SESSION['message'] = "Account not registered. REGISTER FIRST";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRM-ERP</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(0, 0, 0); /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .message {
            margin-bottom: 20px;
        }

        /* Additional button style */
        .modal button {
            background-color:rgb(255, 120, 41);
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }

        .modal button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Sign In to CRM-ERP</h2>

    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</div>

<!-- Modal for error messages -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        <a href="register.php"><button>REGISTER FIRST</button></a>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("errorModal");

    // Get the message element
    var message = "<?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>";

    // Show the modal if there's a message
    if (message) {
        modal.style.display = "block";
    }

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
