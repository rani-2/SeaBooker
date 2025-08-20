<?php

// ✅ Secure Session Handling
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Ensure Admin is Logged In
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin.html");
    exit();
}

// ✅ Database Connection
$host = "localhost";
$username = "root";
$password = "";
$database = "seabooker";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Fetch Boats Added by the Logged-in Owner
$email = $_SESSION["email"];
$query = "SELECT bo.id, bo.boat_name, bo.boat_type, bo.capacity, bo.details, bo.image, bo.created_at, 
          (SELECT COUNT(*) FROM reservations r WHERE r.boat_id = bo.id) AS is_booked
          FROM boats bo WHERE bo.owner_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?>!</h2>

        <!-- Add Boat Button -->
        <a href="add_boat.php" class="add-button">+ Add New Boat</a>

        <h3>Your Registered Boats</h3>
        <table border="1">
            <tr>
                <th>Boat Name</th>
                <th>Boat Type</th>
                <th>Capacity</th>
                <th>Details</th>
                <th>Image</th>
                <th>Registered At</th>
                <th>Booking Status</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["boat_name"]) . "</td>
                            <td>" . htmlspecialchars($row["boat_type"]) . "</td>
                            <td>" . htmlspecialchars($row["capacity"]) . "</td>
                            <td>" . htmlspecialchars($row["details"]) . "</td>
                            <td><img src='" . htmlspecialchars($row["image"]) . "' width='100'></td>
                            <td>" . htmlspecialchars($row["created_at"]) . "</td>
                            <td>" . ($row["is_booked"] > 0 ? "<span style='color:red;'>Booked</span>" : "<span style='color:green;'>Available</span>") . "</td>
                            <td>
                                <a href='delete_boat.php?id=" . $row["id"] . "' onclick=\"return confirm('Are you sure?')\">Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No boats added yet.</td></tr>";
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