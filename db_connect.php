<?php
$host = "localhost"; // XAMPP runs on localhost
$username = "root";  // Default MySQL username
$password = "";      // Default is empty in XAMPP
$database = "seabooker"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment to test the connection
// echo "Connected to MySQL successfully!";
?>
