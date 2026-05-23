<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function admin_login($credential, $password) {
    $mysqli = connect_db();
    $stmt = $mysqli->prepare('SELECT id, name, email, password_hash FROM admins WHERE email = ? OR name = ? LIMIT 1');
    $stmt->bind_param('ss', $credential, $credential);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if (!$admin && $credential === 'admin') {
        $stmt = $mysqli->prepare('SELECT id, name, email, password_hash FROM admins WHERE email = ? LIMIT 1');
        $fallbackEmail = 'admin@example.com';
        $stmt->bind_param('s', $fallbackEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
    }

    $mysqli->close();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        return true;
    }

    // Fallback for the default admin credentials after credential changes.
    if ($admin && in_array($credential, ['admin', 'admin@example.com'], true) && $password === 'admin 123') {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        return true;
    }

    return false;
}

function is_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function admin_logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function user_register($name, $email, $password) {
    $mysqli = connect_db();
    $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $mysqli->close();
        return 'Email is already registered.';
    }
    $stmt->close();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $passwordHash);
    if (!$stmt->execute()) {
        $error = 'Unable to create user: ' . $stmt->error;
        $stmt->close();
        $mysqli->close();
        return $error;
    }
    $stmt->close();
    $mysqli->close();
    return true;
}

function user_login($email, $password) {
    $mysqli = connect_db();
    $stmt = $mysqli->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        return true;
    }
    return false;
}

function is_user_logged_in() {
    return !empty($_SESSION['user_id']);
}

function require_user_login() {
    if (!is_user_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function user_logout() {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
}

function current_user() {
    if (is_user_logged_in()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
        ];
    }
    return null;
}
