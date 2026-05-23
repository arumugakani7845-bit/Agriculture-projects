<?php
require_once __DIR__ . '/common.php';

$mysqli = connect_db();

// Ensure admins table exists
$createSql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$mysqli->query($createSql);

$error = '';
$success = '';

// If there's already an admin, don't allow public registration
$res = $mysqli->query('SELECT COUNT(*) AS c FROM admins');
$count = $res ? (int)$res->fetch_assoc()['c'] : 0;
$res->free();
if ($count > 0) {
    // Redirect to login if admins already exist
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)');
        if (!$stmt) {
            $error = 'Prepare failed: ' . $mysqli->error;
        } else {
            $stmt->bind_param('sss', $name, $email, $hash);
            if ($stmt->execute()) {
                $success = 'Admin account created. You can now log in.';
            } else {
                $error = 'Create failed: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$mysqli->close();

render_admin_header('Create Admin Account');
?>
<div class="card">
    <h2>Create Admin Account</h2>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <p><a class="button" href="login.php">Go to Login</a></p>
    <?php else: ?>
    <form method="post" action="">
        <div class="form-row">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-row">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-row">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-row">
            <label for="confirm">Confirm password</label>
            <input type="password" id="confirm" name="confirm" required>
        </div>
        <button class="button" type="submit">Create Admin</button>
    </form>
    <?php endif; ?>
</div>
<?php render_admin_footer();
