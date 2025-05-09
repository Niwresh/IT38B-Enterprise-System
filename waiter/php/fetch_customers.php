<?php
include 'db_connect.php';

$sql = "SELECT * FROM customers ORDER BY name ASC";
$result = $conn->query($sql);

echo "<table border='1' width='100%'>
<tr><th>Name</th><th>Email</th><th>Phone</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['name']."</td><td>".$row['email']."</td><td>".$row['phone']."</td></tr>";
    }
} else {
    echo "<tr><td colspan='3'>No customers found</td></tr>";
}

echo "</table>";
?>
