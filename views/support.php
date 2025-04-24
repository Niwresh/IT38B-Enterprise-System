<?php include('components/navbar.php'); ?>
<?php include('components/sidebar.php'); ?>

<h2>Support Ticket Management</h2>

<div class="ticket-list">
    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Issue</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($supportTickets as $ticket) : ?>
                <tr>
                    <td><?php echo $ticket['customer_name']; ?></td>
                    <td><?php echo $ticket['issue_description']; ?></td>
                    <td><?php echo $ticket['status']; ?></td>
                    <td>
                        <a href="update_ticket.php?id=<?php echo $ticket['id']; ?>">Update</a>
                        <a href="close_ticket.php?id=<?php echo $ticket['id']; ?>">Close</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('components/footer.php'); ?>
