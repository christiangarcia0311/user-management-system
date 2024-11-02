<?php
$servername = "127.0.0.1:3306"; // localhost port '3306' is port sa database
$username = "root"; // Username is 'root'
$password = "root"; // Password is 'root' di pwede empty
$dbname = "admin_system"; // Pangalan sa database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
