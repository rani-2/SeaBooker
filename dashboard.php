<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_email'])) { // Fixed session variable
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "seabooker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['user_email'];

$stmt = $conn->prepare("SELECT * FROM bookings WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>

    <?php if ($booking) { ?>
        <h3>Your Booking Details</h3>
        
        <p><strong>Departure Date:</strong> 
            <?php echo date("d F Y", strtotime($booking['booking_date'])); ?>
        </p>

        <p><strong>Start Time:</strong> 
            <?php echo date("h:i A", strtotime($booking['start_time'])); ?>
        </p>

        <p><strong>End Time:</strong> 
            <?php echo date("h:i A", strtotime($booking['end_time'])); ?>
        </p>

        <p><strong>Passengers:</strong> 
            <?php echo htmlspecialchars($booking['passengers']); ?>
        </p>
    <?php } else { ?>
        <p>No bookings found.</p>
    <?php } ?>

    <a href="logout.php">Logout</a>
</body>
</html>
