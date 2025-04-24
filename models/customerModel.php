<?php
// Database connection
require_once '../config/database.php';

// Get all customers
function getCustomers() {
    global $conn;
    $query = "SELECT * FROM customers";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Create a new customer
function createCustomer($data) {
    global $conn;
    $query = "INSERT INTO customers (first_name, last_name, email, phone_number, address) 
              VALUES ('{$data['first_name']}', '{$data['last_name']}', '{$data['email']}', '{$data['phone_number']}', '{$data['address']}')";
    return mysqli_query($conn, $query);
}

// Update a customer
function updateCustomer($id, $data) {
    global $conn;
    $query = "UPDATE customers SET first_name = '{$data['first_name']}', last_name = '{$data['last_name']}', email = '{$data['email']}', 
              phone_number = '{$data['phone_number']}', address = '{$data['address']}' WHERE id = {$id}";
    return mysqli_query($conn, $query);
}

// Delete a customer
function removeCustomer($id) {
    global $conn;
    $query = "DELETE FROM customers WHERE id = {$id}";
    return mysqli_query($conn, $query);
}
