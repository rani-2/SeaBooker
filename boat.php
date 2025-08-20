<?php
$conn = new mysqli("localhost", "root", "", "seabooker");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch only boats that are marked as "Visible"
$sql = "SELECT * FROM boats WHERE booking_status = 'Visible'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEABOOKER - Boat Management</title>
    <link rel="stylesheet" href="boat.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>SEABOOKER</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="contact.html">Contact</a></li>
                
            </ul>
        </nav>
    </header>

    <h2>Available Boats</h2>
    <section class="boat-listing">
    
        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class='boat-item'>
                    <img src='<?= htmlspecialchars($row['image']) ?>' alt='<?= htmlspecialchars($row['boat_name']) ?>' width="200">
                    <div class='boat-info'>
                        <h3><?= htmlspecialchars($row['boat_name']) ?></h3>
                        <p>Type: <?= htmlspecialchars($row['boat_type']) ?></p>
                        <p>Capacity: <?= htmlspecialchars($row['capacity']) ?></p>
                        <button onclick="location.href='reserve.php?boat_id=<?= $row['id'] ?>'">Reserve</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No boats available for rental at the moment.</p>
        <?php endif; ?>
    </section>
</body>
</html>

<?php
$conn->close();
?>
