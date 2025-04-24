<?php include('components/navbar.php'); ?>
<?php include('components/sidebar.php'); ?>

<h2>Sales Management</h2>

<div class="sales-list">
    <table>
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Product</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale) : ?>
                <tr>
                    <td><?php echo $sale['customer_name']; ?></td>
                    <td><?php echo $sale['product_name']; ?></td>
                    <td><?php echo $sale['amount']; ?></td>
                    <td><?php echo $sale['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('components/footer.php'); ?>
