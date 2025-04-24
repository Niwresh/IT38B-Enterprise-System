<?php
// Include model
require_once '../models/customerModel.php';

// Get all customers
function getAllCustomers() {
    return getCustomers();
}

// Add a new customer
function addCustomer($data) {
    return createCustomer($data);
}

// Edit a customer
function editCustomer($id, $data) {
    return updateCustomer($id, $data);
}

// Delete a customer
function deleteCustomer($id) {
    return removeCustomer($id);
}
