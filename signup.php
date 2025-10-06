<?php
session_start();
include('config.php'); // Database connection

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Generate simple numeric CAPTCHA for the form (4 digits)
if (empty($_SESSION['signup_captcha'])) {
    $_SESSION['signup_captcha'] = rand(1000, 9999);
}

$message = "";

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "Invalid form submission. Please try again.";
    } else {
        // Read and sanitize inputs
        $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $mobile = preg_replace('/\s+/', '', trim($_POST['mobile'] ?? '')); // remove spaces
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        $captcha_input = trim($_POST['captcha'] ?? '');

        // Server-side validation
        if (strlen($fullname) < 2) {
            $message = "Full name must be at least 2 characters.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format!";
        } elseif (!preg_match('/^[A-Za-z0-9_.]{3,30}$/', $username)) {
            $message = "Username must be 3-30 chars (letters, numbers, . or _).";
        } elseif (!preg_match('/^[7-9][0-9]{9}$/', $mobile)) {
            $message = "Invalid mobile number. Must start with 7, 8, or 9 and be 10 digits.";
        } elseif (strlen($password) < 8) {
            $message = "Password must be at least 8 characters long.";
        } elseif ($password !== $confirm) {
            $message = "Passwords do not match!";
        } elseif (!ctype_digit($captcha_input) || $captcha_input != ($_SESSION['signup_captcha'] ?? '')) {
            $message = "Invalid CAPTCHA. Please enter the number shown.";
            // refresh captcha below
        } else {
            // Check if user already exists (prepared statement)
            $check_sql = "SELECT id FROM users WHERE email = ? OR username = ?";
            $check_stmt = $conn->prepare($check_sql);
            if ($check_stmt === false) {
                $message = "Database error (prepare).";
            } else {
                $check_stmt->bind_param("ss", $email, $username);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    $message = "User with that email or username already exists!";
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert user (prepared)
                    $sql = "INSERT INTO users (fullname, email, username, mobile, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        $message = "Database error (prepare insert).";
                    } else {
                        $stmt->bind_param("sssss", $fullname, $email, $username, $mobile, $hashed_password);
                        if ($stmt->execute()) {
                            // Clear CSRF & captcha so form can't be replayed
                            unset($_SESSION['csrf_token']);
                            unset($_SESSION['signup_captcha']);
                            // Regenerate new tokens for future forms
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                            $_SESSION['signup_captcha'] = rand(1000, 9999);

                            // Redirect to login with success flag
                            header("Location: login.php?signup=success");
                            exit();
                        } else {
                            $message = "Error registering user. Please try again later.";
                        }
                        $stmt->close();
                    }
                }
                $check_stmt->close();
            }
        }
    }

    // If we reach here with an error, regenerate captcha for next attempt
    $_SESSION['signup_captcha'] = rand(1000, 9999);
}

// Fetch last 10 users for display (sanity: use prepared or simple query since no input)
$result = $conn->query("SELECT fullname, email, mobile FROM users ORDER BY id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up - MyHerbalLife</title>
  <link rel="stylesheet" href="style.css" />
  <script>
    // Client-side validation
    function validateForm() {
      const fullname = document.getElementById('fullname').value.trim();
      const email = document.getElementById('email').value.trim();
      const username = document.getElementById('username').value.trim();
      const mobile = document.getElementById('mobile').value.trim();
      const password = document.getElementById('password').value;
      const confirm = document.getElementById('confirm').value;
      const captcha = document.getElementById('captcha').value.trim();

      if (fullname.length < 2) { alert('Full name must be at least 2 characters.'); return false; }
      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(email)) { alert('Invalid email address.'); return false; }
      const userRe = /^[A-Za-z0-9_.]{3,30}$/;
      if (!userRe.test(username)) { alert('Username must be 3-30 chars: letters, numbers, . or _.'); return false; }
      const mobileRe = /^[7-9][0-9]{9}$/;
      if (!mobileRe.test(mobile)) { alert('Mobile must start with 7/8/9 and be 10 digits.'); return false; }
      if (password.length < 8) { alert('Password must be at least 8 characters.'); return false; }
      if (password !== confirm) { alert('Passwords do not match.'); return false; }
      if (!/^\d{4}$/.test(captcha)) { alert('Enter the 4-digit CAPTCHA shown.'); return false; }
      return true;
    }

    // Refresh captcha via simple reload (server generates new)
    function refreshCaptcha() {
      // Just reload the captcha image number by reloading the page fragment
      // We'll ask server to regenerate by calling the same page with ?newcaptcha=1 if needed
      fetch(window.location.pathname + '?newcaptcha=1', {method: 'GET'}).then(() => {
        // reload the portion or full page to update captcha number
        location.reload();
      });
    }
  </script>
  <style>
body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #f6f6f6;
  margin: 0;
  padding: 0;
}

.container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: flex-start;
  gap: 30px;
  padding: 30px;
}

.table-container, .form-container {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 25px 30px;
}

.table-container {
  flex: 1;
  min-width: 350px;
  max-width: 550px;
}

.form-container {
  flex: 1;
  min-width: 350px;
  max-width: 450px;
}

h2 {
  margin-bottom: 15px;
  color: #1a6622;
  border-bottom: 2px solid #1a6622;
  padding-bottom: 8px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

th, td {
  text-align: left;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #1a6622;
  color: #fff;
}

tr:hover {
  background-color: #f9f9f9;
}

label {
  display: block;
  margin-top: 12px;
  font-weight: 600;
}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="tel"],
input[type="number"] {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

button {
  margin-top: 18px;
  width: 100%;
  padding: 10px;
  background-color: #1a6622;
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  background-color: #145718;
}

.error-msg {
  background-color: #ffdede;
  color: #a80000;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 10px;
  font-weight: 500;
}

.signup-link {
  margin-top: 15px;
  text-align: center;
}

.signup-link a {
  color: #1a6622;
  text-decoration: none;
}

.signup-link a:hover {
  text-decoration: underline;
}

/* captcha styling */
.captcha-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
}
.captcha-row input {
  flex: 1;
}

.captcha-row button {
  width: auto;
  padding: 8px 14px;
  background-color: #2e7d32;
  border-radius: 4px;
  font-size: 14px;
}

@media (max-width: 900px) {
  .container {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>

</head>
<body>

<header>
  <div class="logo">MyHerbalLife</div>
  <nav>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
  </nav>
</header>

<div class="container">
  <!-- Left: Stored Data Table -->
  <div class="table-container">
    <h2>Recently Registered Users</h2>
    <table>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
      </tr>
      <?php
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              // escape output
              $n = htmlspecialchars($row['fullname']);
              $e = htmlspecialchars($row['email']);
              $m = htmlspecialchars($row['mobile']);
              echo "<tr>
                      <td>{$n}</td>
                      <td>{$e}</td>
                      <td>{$m}</td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='3'>No users registered yet.</td></tr>";
      }
      ?>
    </table>
  </div>

  <!-- Right: Signup Form -->
  <div class="form-container">
    <h2>Register</h2>

    <?php if ($message != ""): ?>
      <div class="error-msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="" onsubmit="return validateForm()">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

      <label for="fullname">Full Name</label>
      <input type="text" name="fullname" id="fullname" required value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>">

      <label for="email">Email ID</label>
      <input type="email" name="email" id="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">

      <label for="username">Username</label>
      <input type="text" name="username" id="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">

      <label for="mobile">Phone Number</label>
      <input type="tel" name="mobile" id="mobile" required value="<?php echo isset($mobile) ? htmlspecialchars($mobile) : ''; ?>">

      <label for="password">Create Password</label>
      <input type="password" name="password" id="password" required>

      <label for="confirm">Confirm Password</label>
      <input type="password" name="confirm" id="confirm" required>

      <label for="captcha">Enter CAPTCHA: <strong><?php echo htmlspecialchars($_SESSION['signup_captcha']); ?></strong></label>
      <div style="display:flex; gap:8px; align-items:center;">
        <input type="text" name="captcha" id="captcha" required placeholder="Enter number">
        <button type="button" onclick="refreshCaptcha()" style="padding:6px 10px; background:#2e7d32; color:#fff; border:none; border-radius:4px; cursor:pointer;">Refresh</button>
      </div>

      <button type="submit" style="margin-top:12px;">Sign Up</button>
    </form>

    <div class="signup-link">
      <p>Already have an account? <a href="login.php">Login</a></p>
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
