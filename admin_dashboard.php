<?php
session_start();
include('config.php');

// ✅ Access Control — only admin can view this page
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Handle status change
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $current_status = $_GET['toggle'] === 'active' ? 'inactive' : 'active';
    $stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
    $stmt->bind_param("si", $current_status, $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// ✅ Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// ✅ Fetch all users
$result = $conn->query("SELECT id, fullname, email, username, mobile, status, created_at FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - MyHerbalLife</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f3f4f6;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #2e7d32;
      color: white;
      padding: 15px;
      text-align: center;
    }
    h1 {
      margin: 0;
    }
    table {
      width: 95%;
      margin: 30px auto;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #2e7d32;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    .active {
      background-color: #2e7d32;
      color: white;
    }
    .inactive {
      background-color: #e53935;
      color: white;
    }
    .delete {
      background-color: #c62828;
      color: white;
    }
    .logout {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #2e7d32;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      width: fit-content;
    }
    .logout:hover {
      background-color: #1b5e20;
    }
  </style>
</head>
<body>

<header>
  <h1>Admin Dashboard</h1>
  <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👑</p>
</header>

<table>
  <tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Username</th>
    <th>Mobile</th>
    <th>Status</th>
    <th>Registered On</th>
    <th>Actions</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo htmlspecialchars($row['fullname']); ?></td>
      <td><?php echo htmlspecialchars($row['email']); ?></td>
      <td><?php echo htmlspecialchars($row['username']); ?></td>
      <td><?php echo htmlspecialchars($row['mobile']); ?></td>
      <td>
        <a href="?id=<?php echo $row['id']; ?>&toggle=<?php echo $row['status']; ?>" 
           class="btn <?php echo $row['status'] == 'active' ? 'active' : 'inactive'; ?>">
          <?php echo ucfirst($row['status']); ?>
        </a>
      </td>
      <td><?php echo $row['created_at']; ?></td>
      <td>
        <a href="?delete=<?php echo $row['id']; ?>" 
           class="btn delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
      </td>
    </tr>
  <?php } ?>
</table>

<a href="logout.php" class="logout">Logout</a>

</body>
</html>
