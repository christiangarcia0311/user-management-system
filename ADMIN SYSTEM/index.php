<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to User System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .buttons {
            display: flex;
            flex-direction: column;
        }

        .btn {
            display: inline-block;
            background-color: #FFA500; /* Orange */
            color: #ffffff;
            padding: 15px 20px;
            margin: 10px 0;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
            font-weight: bold;
            text-align: center;
        }

        .btn:hover {
            background-color: #e69500; 
            transform: translateY(-2px);
        }

        .admin {
            background-color: #FF4500; 
        }

        .admin:hover {
            background-color: #e63900; 
        }

        .footer {
            margin-top: 20px;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the User Management System</h1>
        <div class="buttons">
            <a href="register.php" class="btn">Register</a>
            <a href="login.php" class="btn">Login</a>
            <a href="admin_login.php" class="btn admin">Admin Login</a>
        </div>
        <div class="footer">
            <p>&copy; 2024 User Management System. Developer</p>
        </div>
    </div>
</body>
</html>
