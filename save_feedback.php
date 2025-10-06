<?php
$conn = new mysqli("localhost", "root", "", "myherballife");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

$sql = "INSERT INTO feedback (name, email, message) VALUES ('$name', '$email', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Thank you for your feedback!'); window.location.href='feedback.php';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
