<?php
require_once __DIR__ . '/common.php';

render_admin_header('Admin Home');

if (!is_logged_in()):
    ?>
    <div class="card">
        <h2>Admin Area</h2>
        <p>You must be logged in to access admin pages.</p>
        <p><a class="button" href="login.php">Login as Admin</a></p>
    </div>
    <?php
    render_admin_footer();
    exit;
endif;

?>
<div class="card">
    <h2>Admin Links</h2>
    <p>
        <a class="button" href="dashboard.php">Dashboard</a>
        <a class="button" href="expenses.php">Expenses</a>
        <a class="button" href="crops.php">Crop Stock</a>
        <a class="button" href="income.php">Income</a>
        <a class="button" href="orders.php">Orders</a>
        <a class="button" href="payments.php">Payments</a>
    </p>
</div>

<?php render_admin_footer();
