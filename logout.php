<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy session completely

// Redirect user back to home page after logout
header("Location: index.php");
exit();
?>
