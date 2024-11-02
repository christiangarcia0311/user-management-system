<?php
session_start();

$errorMessage = ''; // Initialize an error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = new mysqli('127.0.0.1:3306', 'root', 'root', 'admin_system');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement to fetch the user
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $storedPassword);
        $stmt->fetch();

        // Compare the entered password with the stored password
        if ($password === $storedPassword) {
            $_SESSION['user_id'] = $id; // Set session variable
            header('Location: dashboard.php'); // Redirect to dashboard
            exit();
        } else {
            $errorMessage = "Invalid password."; // Set error message
        }
    } else {
        $errorMessage = "Username not found."; // Set error message
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
      /* Error message styling */
.error-message {
    color: #d9534f;
    margin-bottom: 1rem;
    font-weight: bold;
    text-align: center;
}

/* Reset and other styles here */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f4f4f9;
    padding: 1rem;
}

.login-container {
    width: 100%;
    max-width: 400px;
    padding: 2rem;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

input[type="text"]:focus, input[type="password"]:focus {
    border-color: #ff8c00;
    outline: none;
}

button[type="submit"] {
    width: 100%;
    padding: 0.75rem;
    font-size: 1rem;
    color: #fff;
    background-color: #ff8c00;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #e07b00;
}

.links {
    margin-top: 8px;
}

p, a {
    font-size: 0.9rem;
    color: #333;
}

a {
    color: #ff8c00;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color: #e07b00;
}

p {
    margin-top: 1rem;
}

a:visited {
    color: #6d6d6d;
}

@media (max-width: 480px) {
    .login-container {
        padding: 1.5rem;
    }

    h2 {
        font-size: 1.5rem;
    }

    input[type="text"], input[type="password"], button[type="submit"] {
        font-size: 0.9rem;
        padding: 0.6rem;
    }

    p, a {
        font-size: 0.8rem;
    }
}

    </style>
</head>
<body>
    <div class="login-container">
        <h2>User Login</h2>

        <!-- Display error message if it exists -->
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>

        <div class="links">
            <p>Don't have an account? <a href="register.php">Sign Up</a></p>
            <p>Go Back <a href="index.php">Home</p></p>
        </div>
    </div>
</body>
</html>
