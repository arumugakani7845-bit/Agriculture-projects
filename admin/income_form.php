<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$errors = [];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
income_default:
$income = ['source' => '', 'amount' => '', 'income_date' => date('Y-m-d'), 'notes' => ''];
if ($id > 0) {
    $stmt = $mysqli->prepare('SELECT * FROM income_records WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $income = $result->fetch_assoc() ?: $income;
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = trim($_POST['source'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $income_date = $_POST['income_date'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if ($source === '') {
        $errors[] = 'Source is required.';
    }
    if (!is_numeric($amount) || $amount <= 0) {
        $errors[] = 'Enter a valid amount.';
    }
    if ($income_date === '') {
        $errors[] = 'Income date is required.';
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = $mysqli->prepare('UPDATE income_records SET source = ?, amount = ?, income_date = ?, notes = ? WHERE id = ?');
            $stmt->bind_param('sdssi', $source, $amount, $income_date, $notes, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $mysqli->prepare('INSERT INTO income_records (source, amount, income_date, notes) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('sdss', $source, $amount, $income_date, $notes);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: income.php');
        exit;
    }
    $income = ['source' => $source, 'amount' => $amount, 'income_date' => $income_date, 'notes' => $notes];
}
render_admin_header($id > 0 ? 'Edit Income' : 'Add Income');
?>
<div class="card">
    <h2><?php echo $id > 0 ? 'Edit Income Record' : 'Add Income Record'; ?></h2>
    <?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="">
        <div class="form-row">
            <label for="source">Source</label>
            <input type="text" id="source" name="source" value="<?php echo htmlspecialchars($income['source']); ?>" required>
        </div>
        <div class="form-row">
            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" step="0.01" value="<?php echo htmlspecialchars($income['amount']); ?>" required>
        </div>
        <div class="form-row">
            <label for="income_date">Date</label>
            <input type="date" id="income_date" name="income_date" value="<?php echo htmlspecialchars($income['income_date']); ?>" required>
        </div>
        <div class="form-row">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($income['notes']); ?></textarea>
        </div>
        <button class="button" type="submit"><?php echo $id > 0 ? 'Update Income' : 'Save Income'; ?></button>
    </form>
</div>
<?php render_admin_footer();
