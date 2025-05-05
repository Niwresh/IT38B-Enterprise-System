<?php
session_start();
require 'php/db_connect.php';

$user_id = $_SESSION['user_id'];
$menu_item_id = $_POST['menu_item_id'];
$quantity = max(1, intval($_POST['quantity']));

$query = "INSERT INTO cart (user_id, menu_item_id, quantity) 
          VALUES ($user_id, $menu_item_id, $quantity)
          ON DUPLICATE KEY UPDATE quantity = quantity + $quantity";

mysqli_query($conn, $query);
header("Location: view_menu.php");
exit();
