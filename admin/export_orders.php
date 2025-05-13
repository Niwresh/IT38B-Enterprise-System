<?php
require 'php/db_connect.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=order_report_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Order ID', 'Customer First Name', 'Customer Full Name', 'Menu Item', 'Quantity', 'Price', 'Total Price', 'Ordered At', 'Status']);

$query = "
    SELECT 
        o.id,
        u.firstname,
        u.fullname,
        m.name AS menu_item,
        o.quantity,
        o.price,
        o.total_price,
        o.ordered_at,
        o.order_status
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN menu_items m ON o.menu_item_id = m.id
    ORDER BY o.ordered_at DESC
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        $row['firstname'],
        $row['fullname'],
        $row['menu_item'],
        $row['quantity'],
        $row['price'],
        $row['total_price'],
        $row['ordered_at'],
        $row['order_status']
    ]);
}

fclose($output);
exit();
