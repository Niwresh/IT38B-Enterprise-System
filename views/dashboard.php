<?php include('components/navbar.php'); ?>
<?php include('components/sidebar.php'); ?>

<div class="dashboard-container">
    <h2>CRM Dashboard</h2>
    <div class="stats-cards">
        <div class="card">
            <h3>Total Customers</h3>
            <p><?php echo $totalCustomers; ?></p>
        </div>
        <div class="card">
            <h3>Total Sales</h3>
            <p><?php echo $totalSales; ?></p>
        </div>
        <div class="card">
            <h3>Open Support Tickets</h3>
            <p><?php echo $openTickets; ?></p>
        </div>
    </div>
</div>

<?php include('components/footer.php'); ?>
