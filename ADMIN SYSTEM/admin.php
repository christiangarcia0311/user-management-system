<?php
session_start(); 

// CONDITION IF ADMIN IS LOGGED IN
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php'); 
    exit();
}

// DATABASE CONNECTION
include 'config.php';

$conn = new mysqli('127.0.0.1:3306', 'root', 'root', 'admin_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// HANDLING FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate'])) {
        // GENERATE CREDENTIALS
        $id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $username = strtolower(substr($name, 0, 2)) . rand(000000, 99999);
        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        // UPDATE THE GENERATED CREDENTIALS
        $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $password, $id);

        if ($stmt->execute()) {
            // EMAIL SENDER API
            // GITHUB: https://github.com/christiangarcia0311/email-sender-verifier/
            $apiUrl = 'http://emailsender000.pythonanywhere.com/send-email';
            $postData = json_encode([
                'email_reciever' => $email,
                'msg_subject' => 'Your Login Credentials',
                'msg_body' => "Thank You for Registering $name \n\nUsername: $username\nPassword: $password",
                'sender_name' => 'Admin'
            ]);

            // cURL for sending email
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200) {
                echo "<p>Credentials sent successfully!</p>";
            } else {
                echo "<p>Failed to send email: " . htmlspecialchars($response) . "</p>";
            }
        }

        $stmt->close();
    } 
    elseif (isset($_POST['delete'])) {
        // DELETE USER CONDITION
        $id = $_POST['user_id'];
        $conn->query("DELETE FROM users WHERE id=$id");
        echo "<p style='color: green;'>User deleted successfully.</p>";
    } 
    elseif (isset($_POST['update'])) {
        // UPDATE USER CONDITION
        $id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        // UPDATE USER CREDENTIALS
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, username=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $username, $password, $id);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>User updated successfully!</p>";
        } else {
            echo "<p>Error updating user: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

// QUERY TO FETCH USERS
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        color: #333;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    h2 {
        color: #2d74da;
        margin: 20px 0;
        text-align: center;
    }
    .table-container {
        width: 100%;
        max-width: 90%;
        overflow-x: auto;
        margin-top: 20px;
    }
    table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    th, td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }
    th {
        background-color: #2d74da;
        color: #fff;
    }
    td {
        background-color: #fff;
    }
    button {
        padding: 8px 12px;
        border: none;
        color: #fff;
        cursor: pointer;
        border-radius: 4px;
    }
    button[name="generate"] {
        background-color: #e68a00;
    }
    button[name="delete"] {
        background-color: #d9534f;
    }
    button[name="update"] {
        background-color: #5bc0de;
    }
    
    #updateModal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
    }
    #updateForm {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    #updateForm h3 {
        color: #e68a00;
        margin-bottom: 20px;
        text-align: center;
    }
    #updateForm label {
        font-weight: bold;
        margin-top: 10px;
        color: #333;
    }
    #updateForm input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    #updateForm button[type="submit"] {
        background-color: #2d74da;
        width: 100%;
    }
    #updateForm button[type="button"] {
        background-color: #d9534f; 
        width: 100%;
        margin-top: 5px;
    }

    a[href="index.php"] {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background-color: #d9534f;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
    }
    a[href="index.php"]:hover {
        background-color: #c9302c;
    }

    @media (max-width: 768px) {
        h2 {
            font-size: 1.5em;
        }
        .table-container {
            padding: 0 10px;
        }
    }
    
    .no-records {
        color: lightgray;
    }

    </style>
</head>
<body>
    <h2>Admin Pannel</h2>
    <div class="table-container">
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Date Created</t>
                <th>Actions</th>
            </tr>
            <?php if ($result->num_rows > 0) {  ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <form method="POST" action="admin.php">
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                <button type="submit" name="generate">Generate Credentials</button>
                                <button type="submit" name="delete">Delete</button>
                                <button type="button" name="update" onclick="openUpdateForm(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['name']); ?>', '<?php echo htmlspecialchars($row['email']); ?>', '<?php echo htmlspecialchars($row['username']); ?>')">Update</button>
                            </td>
                        </form>
                    </tr>
                <?php } ?>
            <?php } else {  ?>
                <tr>
                    <td colspan="5" style="text-align:center;" class="no-records">No records available</td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <a href="index.php">Logout</a>

    <div id="updateModal" style="display:none;">
        <div id="updateForm">
            <h3>Update User</h3>
            <form method="POST" action="admin.php">
                <input type="hidden" name="user_id" id="update_user_id">
                <label for="update_name">Name:</label>
                <input type="text" name="name" id="update_name" required>
                <label for="update_email">Email:</label>
                <input type="email" name="email" id="update_email" required>
                <label for="update_username">Username:</label>
                <input type="text" name="username" id="update_username" required>
                <label for="update_password">Password:</label>
                <input type="password" name="password" id="update_password" required>
                <button type="submit" name="update">Update</button>
                <button type="button" onclick="closeUpdateForm()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    function openUpdateForm(id, name, email, username) {
        document.getElementById('update_user_id').value = id;
        document.getElementById('update_name').value = name;
        document.getElementById('update_email').value = email;
        document.getElementById('update_username').value = username;
        document.getElementById('updateModal').style.display = 'flex';
    }

    function closeUpdateForm() {
        document.getElementById('updateModal').style.display = 'none';
    }
    </script>
</body>
</html>

<?php $conn->close(); ?>
