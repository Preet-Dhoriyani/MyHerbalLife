<?php
$servername = "localhost";
$username = "root"; // default for XAMPP
$password = "";     // default for XAMPP
$dbname = "myherballife"; // use your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
