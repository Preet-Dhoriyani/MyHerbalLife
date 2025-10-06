<?php
session_start();
include('config.php'); // Database connection

$message = "";

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($username) || empty($password)) {
        $message = "Please enter both username/email and password.";
    } else {
        // Prepare query (can login with username OR email)
        $sql = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify hashed password
            if (password_verify($password, $row['password'])) {

                // ✅ Set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'] ?? 'user'; // role column se

                // ✅ Redirect based on role
                if ($_SESSION['role'] === 'admin') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: index.php"); // user home page
                }
                exit();

            } else {
                $message = "Incorrect password!";
            }
        } else {
            $message = "User not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - MyHerbalLife</title>
  <link rel="stylesheet" href="style.css">
  <style>
body {
  font-family: Arial, sans-serif;
  background: #f2f2f2;
  margin: 0;
  padding: 0;
}

/* Container styling */
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 70vh;
}

/* Form box */
.form-container {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  width: 350px;
}

.form-container h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #2b7a0b;
}

/* Labels and inputs */
.form-container label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
  color: #333;
}

.form-container input[type="text"],
.form-container input[type="password"] {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
}

.form-container button {
  width: 100%;
  padding: 10px;
  background: #2b7a0b;
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s ease;
}

.form-container button:hover {
  background: #256808;
}

.signup-link {
  text-align: center;
  margin-top: 15px;
}

.signup-link a {
  color: #2b7a0b;
  text-decoration: none;
  font-weight: bold;
}

.signup-link a:hover {
  text-decoration: underline;
}

/* Footer fix */
footer {
  background: #222;
  color: #fff;
  padding: 20px 0;
  font-size: 14px;
}
</style>

</head>
<body>

<header>
  <div class="logo">MyHerbalLife</div>
  <nav>
    <a href="index.php">Home</a>
    <a href="signup.php">Sign Up</a>
  </nav>
</header>

<div class="container">
  <div class="form-container">
    <h2>Login</h2>

    <?php if ($message != ""): ?>
      <div class="error-msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="username">Username or Email</label>
      <input type="text" name="username" id="username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Login</button>
    </form>

    <div class="signup-link">
      <p>Don’t have an account? <a href="signup.php">Sign Up</a></p>
      <a href="index.php">← Back to Home</a>
    </div>
  </div>
</div>

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

</body>
</html>
