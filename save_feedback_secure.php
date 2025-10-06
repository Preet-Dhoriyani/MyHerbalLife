<?php
require_once 'config.php';
require_once 'csrf.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: feedback.php');
    exit;
}
if (!isset($_POST['csrf_token']) || !csrf_check($_POST['csrf_token'])) {
    die('Invalid CSRF token');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
    $_SESSION['feedback_error'] = 'Please fill all fields correctly.';
    header('Location: feedback.php');
    exit;
}

// sanitize message for storage (we still use prepared stmt — no need to escape)
$stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $email, $message);

if ($stmt->execute()) {
    $_SESSION['feedback_success'] = 'Thank you for your feedback!';
    header('Location: feedback.php');
    exit;
} else {
    $_SESSION['feedback_error'] = 'Database error.';
    header('Location: feedback.php');
    exit;
}
