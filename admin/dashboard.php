<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();

$counts = [];
$tables = [
    'expenses' => 'Expenses',
    'crop_stock' => 'Crops',
    'income_records' => 'Income',
    'orders' => 'Orders',
    'payments' => 'Payments',
];
foreach ($tables as $table => $label) {
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM {$table}");
    $counts[$label] = $result->fetch_assoc()['total'];
    $result->free();
}
$mysqli->close();

render_admin_header('Admin Dashboard');
?>
<div class="card">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h2>
    <p>Use the navigation above to manage expenses, crop stock, income, orders, and payments.</p>
</div>
<div class="card">
    <h3>Summary</h3>
    <table>
        <thead>
            <tr><th>Module</th><th>Records</th></tr>
        </thead>
        <tbody>
            <?php foreach ($counts as $label => $count): ?>
            <tr>
                <td><?php echo htmlspecialchars($label); ?></td>
                <td><?php echo (int)$count; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php render_admin_footer();
