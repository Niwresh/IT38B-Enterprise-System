<?php
// Database configuration
define('DB_SERVER', 'localhost'); // Database server (typically localhost)
define('DB_USERNAME', 'root');     // Database username (change this based on your setup)
define('DB_PASSWORD', '');         // Database password (change this based on your setup)
define('ES_DB', 'ES_DB');     // Database name (change 'crm_system' to your actual DB name)

// Create connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, ES_DB); // Use the correct constant ES_DB for the database name

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 (supports most characters, including emojis)
$conn->set_charset('utf8mb4');

// Optionally, you could use PDO for more flexibility and better error handling
/*
try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . ES_DB, DB_USERNAME, DB_PASSWORD);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; 
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
*/

// Make sure to use the $conn variable for queries in your models or controllers

?>
