<?php
// pw_test.php - diagnostic. Run once then delete.
include('config.php');
$username = 'admin';

// fetch stored hash
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    echo "User not found.\n";
    exit;
}
$row = $res->fetch_assoc();
$dbhash = $row['password'];

echo "<h2>Stored DB hash (as text)</h2>";
echo "<pre>" . htmlspecialchars($dbhash) . "</pre>";

echo "<h2>Length & byte info</h2>";
echo "strlen(dbhash) = " . strlen($dbhash) . "<br>";
echo "mb_strlen(dbhash) = " . mb_strlen($dbhash) . "<br>";

echo "<h3>Hex bytes (first 100 bytes)</h3><pre>";
$hex = unpack('H*', $dbhash)[1];
echo substr($hex, 0, 200);
echo "</pre>";

echo "<h2>password_verify() test</h2>";
$plain = 'admin123';
var_export(['submitted_plain' => $plain]);
echo "<br>";
var_export(['password_verify_result' => password_verify($plain, $dbhash)]);
echo "<br><br>";

echo "<h2>Generate new hash now (PHP password_hash)</h2>";
$newhash = password_hash($plain, PASSWORD_DEFAULT);
echo "<pre>" . htmlspecialchars($newhash) . "</pre>";
echo "strlen(newhash) = " . strlen($newhash) . "<br>";
echo "password_verify(new->db) = " . (password_verify($plain, $newhash) ? 'TRUE' : 'FALSE') . "<br>";

echo "<hr><p>If password_verify(newhash, plain) is TRUE but password_verify(dbhash, plain) is FALSE, the DB hash does not match the plain password or is corrupted/truncated/altered.</p>";
?>
