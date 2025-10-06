<?php
// register_secure.php
session_start();
require_once 'config.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

// CSRF check
if (!isset($_POST['csrf_token']) || !csrf_check($_POST['csrf_token'])) {
    die('Invalid CSRF token');
}

// Fetch + trim inputs
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$mobile   = trim($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';

// Server-side validation
$errors = [];
if (strlen($fullname) < 2) $errors[] = 'Full name too short.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
if (!preg_match('/^[A-Za-z0-9_\.]{3,30}$/', $username)) $errors[] = 'Invalid username (3-30 chars, letters/numbers/._).';
if (!preg_match('/^[7-9][0-9]{9}$/', $mobile)) $errors[] = 'Invalid mobile number.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
if ($password !== $confirm) $errors[] = 'Passwords do not match.';

if ($errors) {
    $_SESSION['signup_errors'] = $errors;
    header('Location: signup.php');
    exit;
}

// Prevent duplicate user
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->bind_param('ss', $email, $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $_SESSION['signup_errors'] = ['Email or username already exists.'];
    $stmt->close();
    header('Location: signup.php');
    exit;
}
$stmt->close();

// Hash password securely
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user (prepared)
$insert = $conn->prepare("INSERT INTO users (fullname, email, username, mobile, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$insert->bind_param('sssss', $fullname, $email, $username, $mobile, $hash);

if ($insert->execute()) {
    // Optionally unset CSRF token to avoid replay
    unset($_SESSION['csrf_token']);
    header('Location: login.php?signup=1');
    exit;
} else {
    $_SESSION['signup_errors'] = ['Database error. Try again.'];
    header('Location: signup.php');
    exit;
}
