<?php
session_start();

// Session timeout setup (2 minutes)
$timeout_duration = 120;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Welcome - MyHerbalLife</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="wrapper">
        <header>
            <div class="logo">MyHerbalLife</div>
            <nav>
                <a href="product.html">Products</a>
                <a href="profile.html">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <main>
            <div class="welcome-box">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p>You have successfully logged into your dashboard.</p>
                <a href="index.html" class="hero-btn">Go to Home Page</a>
            </div>
        </main>
        <footer>
            <div class="footer-container">
                <div class="footer-column">
                    <h3>Connect</h3>
                    <a href="#">Facebook</a>
                    <a href="#">Instagram</a>
                </div>
                <div class="footer-column">
                    <h3>Help</h3>
                    <a href="#">Contact</a>
                    <a href="#">About</a>
                </div>
                <div class="footer-column">
                    <h3>Details</h3>
                    <p>Mo. no.: 8000033177</p>
                    <p>Email: dhoriyanipreet@gmail.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 MyHerbalLife. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
