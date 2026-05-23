<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$message = '';
if (isset($_GET['action']) && $_GET['action'] === 'update' && !empty($_GET['id']) && !empty($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    $stmt = $mysqli->prepare('UPDATE payments SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $stmt->close();
    $message = 'Payment status updated.';
}
$query = "SELECT p.*, o.id AS order_id, u.name AS user_name, o.status AS order_status
          FROM payments p
          LEFT JOIN orders o ON p.order_id = o.id
          LEFT JOIN users u ON o.user_id = u.id
          ORDER BY p.created_at DESC";
$result = $mysqli->query($query);
$statusOptions = ['Pending', 'Paid', 'Failed', 'Refunded'];
render_admin_header('Payments');
?>
<div class="card">
    <h2>Payment Details</h2>
    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <table>
        <thead>
            <tr><th>#</th><th>Order</th><th>User</th><th>Amount</th><th>Method</th><th>Status</th><th>Paid At</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($payment = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $payment['id']; ?></td>
                <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
                <td><?php echo htmlspecialchars($payment['user_name']); ?></td>
                <td><?php echo number_format($payment['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                <td><?php echo htmlspecialchars($payment['status']); ?></td>
                <td><?php echo htmlspecialchars($payment['paid_at']); ?></td>
                <td>
                    <form method="get" action="payments.php" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?php echo $payment['id']; ?>">
                        <select name="status" style="padding:6px;">
                            <?php foreach ($statusOptions as $status): ?>
                                <option value="<?php echo htmlspecialchars($status); ?>" <?php echo $payment['status'] === $status ? 'selected' : ''; ?>><?php echo htmlspecialchars($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="action" value="update">
                        <button class="button" type="submit">Save</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php
$result->free();
$mysqli->close();
render_admin_footer();
