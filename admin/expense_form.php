<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$errors = [];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$expense = ['description' => '', 'amount' => '', 'expense_date' => date('Y-m-d'), 'notes' => ''];
if ($id > 0) {
    $stmt = $mysqli->prepare('SELECT * FROM expenses WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expense = $result->fetch_assoc() ?: $expense;
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $expense_date = $_POST['expense_date'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if ($description === '') {
        $errors[] = 'Description is required.';
    }
    if (!is_numeric($amount) || $amount <= 0) {
        $errors[] = 'Enter a valid amount.';
    }
    if ($expense_date === '') {
        $errors[] = 'Expense date is required.';
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = $mysqli->prepare('UPDATE expenses SET description = ?, amount = ?, expense_date = ?, notes = ? WHERE id = ?');
            $stmt->bind_param('sdssi', $description, $amount, $expense_date, $notes, $id);
            $stmt->execute();
            $stmt->close();
            header('Location: expenses.php');
            exit;
        } else {
            $stmt = $mysqli->prepare('INSERT INTO expenses (description, amount, expense_date, notes) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('sdss', $description, $amount, $expense_date, $notes);
            $stmt->execute();
            $stmt->close();
            header('Location: expenses.php');
            exit;
        }
    }
}
render_admin_header($id > 0 ? 'Edit Expense' : 'Add Expense');
?>
<div class="card">
    <h2><?php echo $id > 0 ? 'Edit Expense' : 'Add Expense'; ?></h2>
    <?php if ($errors): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-row">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($expense['description']); ?>" required>
        </div>
        <div class="form-row">
            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" step="0.01" value="<?php echo htmlspecialchars($expense['amount']); ?>" required>
        </div>
        <div class="form-row">
            <label for="expense_date">Date</label>
            <input type="date" id="expense_date" name="expense_date" value="<?php echo htmlspecialchars($expense['expense_date']); ?>" required>
        </div>
        <div class="form-row">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($expense['notes']); ?></textarea>
        </div>
        <button class="button" type="submit"><?php echo $id > 0 ? 'Update Expense' : 'Save Expense'; ?></button>
    </form>
</div>
<?php render_admin_footer();
