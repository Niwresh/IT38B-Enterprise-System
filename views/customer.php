<?php include('components/navbar.php'); ?>
<?php include('components/sidebar.php'); ?>

<h2>Customer Management</h2>

<div class="customer-list">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer) : ?>
                <tr>
                    <td><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></td>
                    <td><?php echo $customer['email']; ?></td>
                    <td><?php echo $customer['phone_number']; ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?php echo $customer['id']; ?>">Edit</a>
                        <a href="delete_customer.php?id=<?php echo $customer['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('components/footer.php'); ?>
