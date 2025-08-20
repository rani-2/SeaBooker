<?php
session_start();
include "db_connect.php"; // Ensure this includes your database connection

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<p>Error: You must be logged in to view this page.</p>";
    echo "<a href='login.php'>Login</a>"; // Redirect to login page if needed
    exit();
}

$email = $_SESSION['email'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : "Guest";

// Fetch all bookings for the logged-in user
$sql = "SELECT booking_date, start_time, end_time, passengers FROM bookings WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Welcome, " . htmlspecialchars($full_name) . "</h2>";
echo "<h3>Your Booking Details</h3>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p><strong>Departure Date:</strong> " . date("d M Y", strtotime($row['booking_date'])) . "<br>";
        echo "<strong>Start Time:</strong> " . date("h:i A", strtotime($row['start_time'])) . "<br>";
        echo "<strong>End Time:</strong> " . date("h:i A", strtotime($row['end_time'])) . "<br>";
        echo "<strong>Passengers:</strong> " . $row['passengers'] . "</p>";
        echo "<hr>"; // Adds a separator between bookings
    }
} else {
    echo "<p>No bookings found.</p>";
}

$stmt->close();
$conn->close();
?>


<a href='index.html'>Logout</a>
