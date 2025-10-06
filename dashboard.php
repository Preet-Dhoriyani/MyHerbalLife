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

// Delete feedback if delete button clicked
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM feedback WHERE id=$id");
    header("Location: dashboard.php");
    exit();
}

$result = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Dashboard - MyHerbalLife</title>
    <style>
        body {font-family: Arial, sans-serif; background: #f5f5f5; color:#333; margin:0; padding:20px;}
        .container {max-width:1000px; margin:auto; background:white; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
        h1 {text-align:center; color:#2e7d32;}
        table {width:100%; border-collapse:collapse; margin-top:20px;}
        th, td {padding:10px; border-bottom:1px solid #ddd; text-align:left;}
        th {background:#2e7d32; color:white;}
        tr:hover {background:#f1f1f1;}
        .top-links {text-align:right; margin-bottom:15px;}
        a {color:#2e7d32; text-decoration:none; margin-left:10px;}
        a:hover {text-decoration:underline;}
        .delete-btn {
            color: white;
            background: #d32f2f;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        .delete-btn:hover {
            background: #b71c1c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-links">
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> |
            <a href="index.html">Home</a> |
            <a href="logout.php">Logout</a>
        </div>
        <h1>Feedback Dashboard 🌿</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Submitted At</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['message']); ?></td>
                <td><?php echo $row['submitted_at']; ?></td>
                <td>
                    <a href="dashboard.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this feedback?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>     
</html>
