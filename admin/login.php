<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email or username and password are required.';
    } elseif (admin_login($email, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef3ee; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-card { background: white; padding: 32px; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.1); width: 360px; }
        .login-card h1 { margin-top: 0; font-size: 24px; }
        .form-row { margin-bottom: 16px; }
        .form-row label { display: block; margin-bottom: 6px; }
        .form-row input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .button { width: 100%; padding: 12px; border: none; border-radius: 4px; background: #2d7a2d; color: white; font-size: 16px; cursor: pointer; }
        .button:hover { background: #245d24; }
        .alert { padding: 12px; margin-bottom: 16px; border-radius: 4px; background: #fde2e2; color: #912b2b; }
    </style>
</head>
<body>
<div class="login-card">
    <h1>Admin Login</h1>
    <?php if ($error): ?>
        <div class="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-row">
            <label for="email">Email or username</label>
            <input type="text" name="email" id="email" required>
        </div>
        <div class="form-row">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button class="button" type="submit">Login</button>
    </form>
    <div style="margin-top:16px; color:#555; font-size:0.95rem;">
        <p>Default admin access: <strong>admin</strong> / <strong>admin 123</strong></p>
        <p>If you want to create a new admin account, <a href="register.php">register here</a>.</p>
        <p>Regular user login is available at <a href="../login.php">/login.php</a>.</p>
    </div>
</div>
</body>
</html>
