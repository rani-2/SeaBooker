<?php
session_start();
$conn = new mysqli("localhost", "root", "", "seabooker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION["user_email"])) {
        die("<script>alert('You must be logged in to book a boat.'); window.location.href='index.html';</script>");
    }

    $full_name = $_POST["full_Name"];
    $phone = $_POST["phone"];
    $passengers = $_POST["passengers"];
    $booking_datetime = $_POST["booking_datetime"];
    $user_email = $_SESSION["user_email"];

    // Find an existing pending reservation for this user
    $query = "SELECT id FROM reservations WHERE user_email = ? AND status = 'Pending' ORDER BY reserved_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($reservation_id);
    $stmt->fetch();
    $stmt->close();

    if ($reservation_id) {
        // Update existing reservation
        $update_query = "UPDATE reservations SET status = 'Confirmed', full_name = ?, phone = ?, passengers = ?, booking_datetime = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssisi", $full_name, $phone, $passengers, $booking_datetime, $reservation_id);
    } else {
        // Insert a new reservation
        $insert_query = "INSERT INTO reservations (user_email, full_name, phone, passengers, booking_datetime, status, reserved_at) 
                         VALUES (?, ?, ?, ?, ?, 'Confirmed', NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssis", $user_email, $full_name, $phone, $passengers, $booking_datetime);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Reservation successful!'); window.location.href='user_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='booking.html';</script>";
    }

    $stmt->close();
}

$conn->close();
