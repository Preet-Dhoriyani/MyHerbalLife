<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home - MyHerbalLife</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="wrapper">

    <header>
      <div class="logo">MyHerbalLife</div>
      <nav>
        <?php if (isset($_SESSION['username'])): ?>
          <a href="dashboard.php">Dashboard</a>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="signup.php">Signup</a>
        <?php endif; ?>
        <a href="events.php">Events</a>
        <a href="product.php">Products</a>
        <a href="points.php">Point</a>
        <a href="faq.php">FAQ</a>
        <a href="feedback.php">Feedback</a>
        <a href="profile.php">Profile</a>
        <a href="offlineshop.php">Offline Shop</a>
      </nav>
    </header>

    <main>
      <section class="hero">
        <div class="hero-content">
          <h1>Welcome to MyHerbalLife</h1>
          <p>Your wellness journey starts here.</p>

          <?php if (isset($_SESSION['username'])): ?>
            <a href="dashboard.php" class="hero-btn">Go to Dashboard</a>
          <?php else: ?>
            <a href="login.php" class="hero-btn">Get Started</a>
          <?php endif; ?>

        </div>
      </section>
    </main>

    <footer>
      <div class="footer-container">
        <div class="footer-column">
          <h3>Connect</h3>
          <a href="https://www.facebook.com/HerbalifeIndiaOfficial/">Facebook</a>
          <a href="https://www.instagram.com/herbalifeindiaofficial/">Instagram</a>
          <a href="https://x.com/herbalife">Twitter</a>
          <a href="https://www.youtube.com/Herbalife">YouTube</a>
        </div>

        <div class="footer-column">
          <h3>Help</h3>
          <a href="contact.php">Contact</a>
          <a href="about.php">About</a>
          <a href="privacy-policy.php">Privacy Policy</a>
          <a href="terms-of-use.php">Terms Of Use</a>
        </div>

        <div class="footer-column">
          <h3>Learn</h3>
          <a href="usage.php">How to make a shake</a>
        </div>

        <div class="footer-column">
          <h3>Details</h3>
          <p>Mo. no.: 8000033177</p>
          <p>Email: dhoriyanipreet@gmail.com</p>
          <p>Name: Preet Dhoriyani</p>
        </div>
      </div>

      <div class="footer-bottom">
        <p>© 2025 MyHerbalLife. All rights reserved.</p>
      </div>
    </footer>

  </div>
</body>

</html>
