<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "seabooker");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validation
    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        die("Error: Name should contain only alphabets and spaces.");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/@gmail\.com$/", $email)) {
        die("Error: Email must be a valid Gmail address.");
    } elseif (empty($password)) {
        die("Error: Password cannot be empty.");
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User found, check password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Store session
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['name'];

            // Redirect to dashboard
            header("Location: user_dashboard.php");
            exit();
        } else {
            die("Error: Incorrect password.");
        }
    } else {
        // If user not found, show register message
        die("Error: You are not registered. Please <a href='register.php'>register here</a>.");
    }
}

$conn->close();
?>
