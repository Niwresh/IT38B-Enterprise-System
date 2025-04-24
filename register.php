<?php
include 'connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['signUp'])) {
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $Email = $_POST['email'];
    $Password = $_POST['password'];

    $hashedPassword = password_hash($Password, PASSWORD_DEFAULT); 

    $checkEmail = "SELECT * FROM users WHERE Email=?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        $insertQuery = "INSERT INTO users (Fname, Lname, Email, Password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssss", $Fname, $Lname, $Email, $hashedPassword); 

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

if (isset($_POST['signIn'])) {
    $Email = strtolower($_POST['email']);
    $Password = $_POST['password'];

    if (empty($Email) || empty($Password)) {
        echo "Email and Password cannot be empty.";
        exit();
    }

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        echo "<pre>";
        print_r($row);
        echo "</pre>";

        if (password_verify($Password, $row['Password'])) {
            echo "Password verified!";
            $_SESSION['Email'] = $row['Email'];
            header("Location: homepage.php");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No user found with this email.";
    }
}

?>
