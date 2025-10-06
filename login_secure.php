<?php
session_start();
require_once 'config.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}
if (!isset($_POST['csrf_token']) || !csrf_check($_POST['csrf_token'])) {
    die('Invalid CSRF token');
}

// simple rate limit
if (!isset($_SESSION['failed_login'])) $_SESSION['failed_login'] = 0;
if ($_SESSION['failed_login'] >= 5) {
    die('Too many failed login attempts. Try again later.');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Input validation
if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Enter username/email and password.';
    header('Location: login.php');
    exit;
}

// Lookup user by username or email
$stmt = $conn->prepare("SELECT id, username, fullname, password FROM users WHERE username = ? OR email = ?");
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    $user = $res->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // success
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['LAST_ACTIVITY'] = time();
        $_SESSION['failed_login'] = 0;
        header('Location: dashboard.php');
        exit;
    } else {
        $_SESSION['failed_login']++;
        $_SESSION['login_error'] = 'Invalid credentials.';
        header('Location: login.php');
        exit;
    }
} else {
    $_SESSION['failed_login']++;
    $_SESSION['login_error'] = 'Invalid credentials.';
    header('Location: login.php');
    exit;
}
