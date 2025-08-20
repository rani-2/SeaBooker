<?php
session_start();
$conn = new mysqli("localhost", "root", "", "seabooker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Ensure user is logged in
if (!isset($_SESSION["user_email"])) {
    die("<script>alert('You must be logged in to reserve a boat.'); window.location.href='index.html';</script>");
}

// ✅ Ensure boat_id is provided
if (!isset($_GET["boat_id"]) || empty($_GET["boat_id"])) {
    die("<script>alert('Invalid request. Boat ID is missing.'); window.location.href='boat.php';</script>");
}

$boat_id = intval($_GET["boat_id"]); // ✅ Ensure it's an integer
$user_email = $_SESSION["user_email"];

// ✅ Check if boat exists in boats table
$check_boat = $conn->prepare("SELECT id FROM boats WHERE id = ?");
$check_boat->bind_param("i", $boat_id);
$check_boat->execute();
$check_boat->store_result();
if ($check_boat->num_rows == 0) {
    die("<script>alert('Error: Boat does not exist.'); window.location.href='boat.php';</script>");
}
$check_boat->close();

// ✅ Insert reservation into reservations table
$query = "INSERT INTO reservations (boat_id, user_email, status, reserved_at) VALUES (?, ?, 'Pending', NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $boat_id, $user_email);

if ($stmt->execute()) {
    // ✅ Redirect to booking.html with the boat_id
    header("Location: booking.html?boat_id=$boat_id");
    exit();
} else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='boat.php';</script>";
}

$stmt->close();
$conn->close();
?>