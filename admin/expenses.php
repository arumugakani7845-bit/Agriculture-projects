<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$message = '';
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare('DELETE FROM expenses WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $message = 'Expense record deleted successfully.';
}
$result = $mysqli->query('SELECT * FROM expenses ORDER BY expense_date DESC');

render_admin_header('Expenses');
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Expenses</h2>
        <a class="button" href="expense_form.php">Add Expense</a>
    </div>
    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <table>
        <thead>
            <tr><th>#</th><th>Description</th><th>Amount</th><th>Date</th><th>Notes</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($expense = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $expense['id']; ?></td>
                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                <td><?php echo number_format($expense['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                <td><?php echo htmlspecialchars($expense['notes']); ?></td>
                <td>
                    <a class="button" href="expense_form.php?id=<?php echo $expense['id']; ?>">Edit</a>
                    <a class="button" href="expenses.php?action=delete&id=<?php echo $expense['id']; ?>" onclick="return confirm('Delete this expense?');">Delete</a>
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
