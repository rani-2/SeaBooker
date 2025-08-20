<?php
session_start();
require 'db_connect.php';  // Your database connection file

// Check if boat ID is set
if (!isset($_GET["id"])) {
    die("Error: Boat ID not provided.");
}

$boat_id = $_GET["id"];

// Delete query
$query = "DELETE FROM boats WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $boat_id);

if ($stmt->execute()) {
    echo "<script>alert('Boat deleted successfully!'); window.location='admin_dashboard.php';</script>";
} else {
    echo "Error deleting boat: " . $conn->error;
}

$stmt->close();
$conn->close();
?>