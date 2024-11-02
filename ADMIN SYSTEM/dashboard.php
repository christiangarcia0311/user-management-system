<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('127.0.0.1:3306', 'root', '', 'admin_system');
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, username, created_at FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $username, $created_at);
$stmt->fetch();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2d74da;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            font-size: 14px;
            margin: 10px 0;
            color: #333;
        }

        strong {
            color: #b2b2b2;
        }

        .logout {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff5722;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .logout:hover {
            background-color: #e64a19;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($name); ?></h2>
        <p>Username: <strong><?php echo htmlspecialchars($username); ?></strong></p>
        <p>Date Created: <strong><?php echo htmlspecialchars($created_at); ?></strong></p>
        <a class="logout" href="logout.php">Logout</a>
    </div>
</body>
</html>
