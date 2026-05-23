<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$message = '';
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare('DELETE FROM income_records WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $message = 'Income record deleted successfully.';
}
$result = $mysqli->query('SELECT * FROM income_records ORDER BY income_date DESC');
render_admin_header('Income');
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Income Records</h2>
        <a class="button" href="income_form.php">Add Income</a>
    </div>
    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <table>
        <thead>
            <tr><th>#</th><th>Source</th><th>Amount</th><th>Date</th><th>Notes</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($income = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $income['id']; ?></td>
                <td><?php echo htmlspecialchars($income['source']); ?></td>
                <td><?php echo number_format($income['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($income['income_date']); ?></td>
                <td><?php echo htmlspecialchars($income['notes']); ?></td>
                <td>
                    <a class="button" href="income_form.php?id=<?php echo $income['id']; ?>">Edit</a>
                    <a class="button" href="income.php?action=delete&id=<?php echo $income['id']; ?>" onclick="return confirm('Delete this income record?');">Delete</a>
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
