<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "seabooker";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boat_id = $_POST["boat_id"];
    $status = $_POST["status"];

    $update_query = "UPDATE boat_owners SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $boat_id);

    if ($stmt->execute()) {
        echo "<script>alert('Boat visibility updated!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating visibility');</script>";
    }
    $stmt->close();
}

$conn->close();
?>