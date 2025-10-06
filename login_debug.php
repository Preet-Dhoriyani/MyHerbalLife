<?php
// login_debug.php (temporary — remove after debugging)
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    echo "<h3>DEBUG INFO</h3>";
    echo "<b>Submitted username/email:</b> " . htmlspecialchars($username) . "<br>";
    echo "<b>Submitted password (raw):</b> " . htmlspecialchars($password) . "<br><hr>";

    $sql = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        echo "<b>Row found (id):</b> " . $row['id'] . "<br>";
        echo "<b>DB username:</b> " . htmlspecialchars($row['username']) . "<br>";
        echo "<b>DB email:</b> " . htmlspecialchars($row['email']) . "<br>";
        echo "<b>Stored hash:</b> " . htmlspecialchars($row['password']) . "<br><hr>";

        $verify = password_verify($password, $row['password']) ? 'TRUE' : 'FALSE';
        echo "<b>password_verify result:</b> " . $verify . "<br>";

        // Also show raw comparison (should be FALSE if DB contains hash)
        echo "<b>raw equality ($password == db_password):</b> " . (($password === $row['password']) ? 'TRUE' : 'FALSE') . "<br>";
    } else {
        echo "<b>No user found for that username/email.</b><br>";
    }

    echo "<hr><a href='login_debug.php'>Back</a>";
    exit;
}
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login Debug</title></head>
<body>
  <h2>Login Debug</h2>
  <form method="post">
    Username or Email: <input name="username"><br><br>
    Password: <input name="password" type="password"><br><br>
    <button type="submit">Submit</button>
  </form>
</body>
</html>
    