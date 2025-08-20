<?php
include 'db_connect.php';

$sql = "SELECT id, name, email, message, submitted_at FROM contact_messages";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<strong>" . $row["name"] . " (" . $row["email"] . ")</strong><br>";
        echo "<p>" . $row["message"] . "</p>";
        echo "<small>Submitted on: " . $row["submitted_at"] . "</small><hr>";
    }
} else {
    echo "No messages yet.";
}

$conn->close();
?>
