<?php
include 'db_connect.php';

$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);

echo "<table border='1' width='100%'>
<tr><th>Order ID</th><th>Customer</th><th>Status</th><th>Date</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id']."</td><td>".$row['customer_name']."</td><td>".$row['status']."</td><td>".$row['order_date']."</td></tr>";
    }
} else {
    echo "<tr><td colspan='4'>No orders found</td></tr>";
}

echo "</table>";
?>
