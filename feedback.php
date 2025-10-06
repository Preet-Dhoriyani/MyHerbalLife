<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Feedback - MyHerbalLife</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="login-box">
    <h2 class="form-title">Share Your Feedback 🌿</h2>
    <form method="POST" action="save_feedback.php" class="login-form">
      <label for="name">Your Name</label>
      <input type="text" name="name" required />

      <label for="email">Your Email</label>
      <input type="email" name="email" required />

      <label for="message">Message</label>
      <textarea name="message" rows="4" required></textarea>

      <button type="submit">Submit</button>
      <div class="signup-link"><a href="index.html">Back to Home</a></div>
    </form>
  </div>
</body>
</html>
