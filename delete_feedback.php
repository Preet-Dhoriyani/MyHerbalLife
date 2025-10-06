<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "myherballife");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM feedback WHERE id=$id");
}

header("Location: dashboard.php");
exit();
?>
