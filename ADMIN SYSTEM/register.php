<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4; 
        margin: 0;
        padding: 0;
        height: 90vh;
      }

      h2 {
        color: #000000;
        text-align: center;
        margin-top: 50px;
      }

      form {
        max-width: 250px;
        margin: 200px auto;
        padding: 20px;
        background: white; 
        border-radius: 8px; 
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }

      label {
        display: block;
        margin-bottom: 5px;
        color: #333; 
      }

      input[type="text"], input[type="email"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd; 
        border-radius: 4px;
        box-sizing: border-box;
        transition: border-color 0.3s;
      }

      input[type="text"]:focus, input[type="email"]:focus {
        border-color: #ff5722; 
      }

      button {
        width: 100%;
        padding: 10px;
        background-color: #ff8c00; 
        color: white; 
        border: none; 
        border-radius: 4px; 
        cursor: pointer; 
        font-size: 16px; 
        transition: background-color 0.3s; 
      }

      button:hover {
        background-color: #e07b00; 
      }

      p {
        text-align: center; 
        color: green; 
      }

      .error-message {
        color: red; 
        text-align: center; 
      }
    </style>
</head>
<body>
    <?php
    include 'config.php';

    $message = "";
    $redirect = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];

        $conn = new mysqli('127.0.0.1:3306', 'root', 'root', 'admin_system');

        if ($conn->connect_error) {
            $message = "<p class='error-message'>Connection failed: " . $conn->connect_error . "</p>";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? OR email = ?");
            $stmt->bind_param("ss", $name, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "<p class='error-message'>Name or email already exists. Please choose a different one.</p>";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email, username, password) VALUES (?, ?, '', '')");
                $stmt->bind_param("ss", $name, $email);

                if ($stmt->execute()) {
                    $message = "<p>Registration successful! Await admin approval and check your email.</p>";
                    $redirect = true; 
                } else {
                    $message = "<p class='error-message'>Error: " . $stmt->error . "</p>";
                }
            }

            $stmt->close();
            $conn->close();
        }
    }
    ?>

    <form method="POST" action="register.php">
        <h2>User Registration</h2>
        <input type="text" name="name" placeholder="Enter name" required>
        <input type="email" name="email" placeholder="Enter email" required>
        <button type="submit">Register</button>
        <p><?php echo $message; ?></p> 
    </form>

    <?php if ($redirect): ?>
        <script>
            let countdown = 5; 
            const messageElement = document.querySelector('p');
            messageElement.innerHTML += "<br>Redirecting to login in " + countdown + " seconds...";
            const interval = setInterval(() => {
                countdown--;
                messageElement.innerHTML = "Redirecting to login in " + countdown + " seconds...";
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = 'login.php'; 
                }
            }, 1000);
        </script>
    <?php endif; ?>
</body>
</html>
