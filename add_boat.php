<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    die("Error: Not logged in. Please login again.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "seabooker";

    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $email = $_SESSION["email"]; // Admin email
    $phone = $_POST["phone"];
    $boat_name = $_POST["boat_name"];
    $boat_type = $_POST["boat_type"];
    $boat_capacity = $_POST["boat_capacity"];
    $boat_details = $_POST["boat_details"];
    $registered_at = date("Y-m-d H:i:s");

    // *Handle image upload*
    $image = $_FILES["image"]["name"];
    $target = "uploads/" . basename($image); // Set upload path

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
        // Insert into database with status "Visible"
        $query = "INSERT INTO boats (owner_email, phone, boat_name, boat_type, capacity, details, image, booking_status, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'Visible', ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $email, $phone, $boat_name, $boat_type, $boat_capacity, $boat_details, $target, $registered_at);

        if ($stmt->execute()) {
            echo "<script>alert('Boat added successfully!'); window.location.href='admin_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error adding boat: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Failed to upload image!');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Boat</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container">
    <h2>Add New Boat</h2>
    <form method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <!-- Phone -->
        <label>Phone:</label>
        <input type="text" name="phone" pattern="\d{10}" 
               title="Phone number must be exactly 10 digits." required>

        <!-- Boat Name -->
        <label>Boat Name:</label>
        <input type="text" name="boat_name" 
               pattern="^(?=.*[A-Za-z])[A-Za-z0-9 ]+$" 
               title="Boat name must contain alphabets. Numbers allowed, special characters not allowed." required>

        <!-- Boat Type -->
        <label>Boat Type:</label>
        <input type="text" name="boat_type" 
               pattern="[A-Za-z ]+" 
               title="Boat type should contain only alphabets." required>

        <!-- Capacity -->
        <label>Capacity</label>
        <input type="number" name="boat_capacity" required>

        <!-- Details -->
        <label>Details:</label>
        <textarea name="boat_details" id="boat_details" required></textarea>

        <!-- Image -->
        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        
        <button type="submit">Add Boat</button>
    </form>
</div>

<!-- JavaScript Validation -->
<script>
function validateForm() {
    const details = document.getElementById('boat_details').value.trim();

    // Regex: Must contain at least one alphabet, numbers allowed, special chars not allowed
    const regex = /^(?=.*[A-Za-z])[A-Za-z0-9 ]+$/;

    if (!regex.test(details)) {
        alert("Details must contain alphabets. Numbers allowed, special characters not allowed.");
        return false; // Prevent form submission
    }
    return true;
}
</script>



</body>
</html>