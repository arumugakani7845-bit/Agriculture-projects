<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$message = '';
if (isset($_GET['action']) && $_GET['action'] === 'update' && !empty($_GET['id']) && !empty($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    $stmt = $mysqli->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $stmt->close();
    $message = 'Order status updated.';
}
$query = "SELECT o.*, u.name AS user_name,
            GROUP_CONCAT(CONCAT(oi.quantity, '× ', COALESCE(cs.crop_name, 'Deleted Crop')) SEPARATOR ', ') AS products
          FROM orders o
          JOIN users u ON u.id = o.user_id
          LEFT JOIN order_items oi ON oi.order_id = o.id
          LEFT JOIN crop_stock cs ON cs.id = oi.crop_id
          GROUP BY o.id
          ORDER BY o.created_at DESC";
$result = $mysqli->query($query);
$statusOptions = ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'];
render_admin_header('Orders');
?>
<div class="card">
    <h2>Manage Orders</h2>
    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <table>
        <thead>
            <tr><th>#</th><th>User</th><th>Products</th><th>Total</th><th>Status</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                <td><?php echo htmlspecialchars($order['products']); ?></td>
                <td><?php echo number_format($order['total_price'], 2); ?></td>
                <td><?php echo htmlspecialchars($order['status']); ?></td>
                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                <td>
                    <form method="get" action="orders.php" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                        <select name="status" style="padding:6px;">
                            <?php foreach ($statusOptions as $status): ?>
                                <option value="<?php echo htmlspecialchars($status); ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>><?php echo htmlspecialchars($status); ?></option>
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
