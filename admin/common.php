<?php
require_once __DIR__ . '/../includes/auth.php';

function render_admin_header($title = 'Admin Dashboard') {
    $current = basename($_SERVER['PHP_SELF']);
    $menu = [
        'dashboard.php' => 'Dashboard',
        'expenses.php' => 'Expenses',
        'crops.php' => 'Crop Stock',
        'income.php' => 'Income',
        'orders.php' => 'Orders',
        'payments.php' => 'Payments',
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f5f5f5; }
        .topbar { background: #2d7a2d; color: white; padding: 16px; }
        .topbar a { color: white; margin-right: 16px; text-decoration: none; }
        .container { padding: 24px; }
        .card { background: white; border-radius: 6px; padding: 20px; box-shadow: 0 0 12px rgba(0,0,0,0.05); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table th { background: #f0f0f0; }
        .button { display: inline-block; padding: 10px 16px; text-decoration: none; margin: 4px 0; background: #2d7a2d; color: white; border-radius: 4px; }
        .button:hover { background: #245d24; }
        .form-row { margin-bottom: 12px; }
        .form-row label { display: block; margin-bottom: 4px; font-weight: bold; }
        .form-row input[type="text"], .form-row input[type="number"], .form-row input[type="date"], .form-row textarea, .form-row select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 16px; }
        .alert-success { background-color: #e3f7e3; color: #216321; }
        .alert-error { background-color: #fde2e2; color: #912b2b; }
    </style>
</head>
<body>
<div class="topbar">
    <strong><?php echo htmlspecialchars($title); ?></strong>
    <?php foreach ($menu as $file => $label): ?>
        <a href="<?php echo $file; ?>" <?php echo $current === $file ? 'style="text-decoration: underline;"' : ''; ?>><?php echo $label; ?></a>
    <?php endforeach; ?>
    <a href="logout.php" style="float:right;">Logout</a>
</div>
<div class="container">
<?php
}

function render_admin_footer() {
    echo "</div></body></html>";
}
