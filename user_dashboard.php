<?php
session_start();
if (!isset($_SESSION["user_email"])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "seabooker";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle cancellation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancel_id"])) {
    $cancel_id = $_POST["cancel_id"];
    $cancel_query = "DELETE FROM reservations WHERE id = ?";
    $cancel_stmt = $conn->prepare($cancel_query);
    $cancel_stmt->bind_param("i", $cancel_id);
    if ($cancel_stmt->execute()) {
        echo "<script>alert('Booking cancelled successfully!'); window.location.href='user_dashboard.php';</script>";
    }
    $cancel_stmt->close();
}

// Get logged-in user's email
$user_email = $_SESSION["user_email"];

// Fetch user's booked boats
$query = "SELECT r.id, b.boat_name, b.boat_type, b.capacity, b.image, r.booking_datetime, r.status 
          FROM reservations r
          JOIN boats b ON r.boat_id = b.id
          WHERE r.user_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user_dashboard.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="boat.php">Book a Boat</a></li>
            <li><a href="index.html">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h2>
        <h3>Your Boat Reservations</h3>

        <table border="1">
            <tr>
                <th>Boat Name</th>
                <th>Type</th>
                <th>Capacity</th>
                <th>Image</th>
                <th>Booking Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["boat_name"]) . "</td>
                            <td>" . htmlspecialchars($row["boat_type"]) . "</td>
                            <td>" . htmlspecialchars($row["capacity"]) . "</td>
                            <td><img src='" . htmlspecialchars($row["image"]) . "' width='100'></td>
                            <td>" . htmlspecialchars($row["booking_datetime"]) . "</td>
                            <td>" . htmlspecialchars($row["status"]) . "</td>
                            <td>
                                <form method='POST' action=''>
                                    <input type='hidden' name='cancel_id' value='" . $row["id"] . "'>
                                    <button type='submit'>Cancel</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No bookings yet.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
