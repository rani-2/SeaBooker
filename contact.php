<?php
// Database connection
$host = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$database = "seabooker";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$name = $phone = $problem = $question = "";
$nameErr = $phoneErr = $problemErr = $questionErr = "";
$successMsg = "";

// Validate form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Validate Name (Only alphabets and spaces allowed)
    if (empty($_POST['name'])) {
        $nameErr = "Name is required";
        $isValid = false;
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $_POST['name'])) {
        $nameErr = "Only alphabets and spaces allowed";
        $isValid = false;
    } else {
        $name = $_POST['name'];
    }

    // Validate Phone (Only numbers, 10 digits)
    if (empty($_POST['phone'])) {
        $phoneErr = "Phone number is required";
        $isValid = false;
    } elseif (!preg_match("/^[0-9]{10}$/", $_POST['phone'])) {
        $phoneErr = "Phone number must be 10 digits";
        $isValid = false;
    } else {
        $phone = $_POST['phone'];
    }

    // Validate Problem (Should not be empty)
    if (empty($_POST['problem'])) {
        $problemErr = "Problem description is required";
        $isValid = false;
    } else {
        $problem = $_POST['problem'];
    }

    // Validate Question (Should not be empty)
    if (empty($_POST['question'])) {
        $questionErr = "Please select a predefined question";
        $isValid = false;
    } else {
        $question = $_POST['question'];
    }

    // If all fields are valid, insert into database
    if ($isValid) {
        $sql = "INSERT INTO contact (name, phone, problem, question) 
                VALUES ('$name', '$phone', '$problem', '$question')";

        if ($conn->query($sql) === TRUE) {
            $successMsg = "Message sent successfully!";
            // Reset fields after successful submission
            $name = $phone = $problem = $question = "";
        } else {
            $successMsg = "Error: " . $conn->error;
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data (store in database, send email, etc.)
        
        echo "<h2>Thank you! Your message has been submitted successfully.</h2>";
        exit; // Stop further execution, so the form is not displayed again
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link rel="stylesheet" href="contact.css">
    <style>
        .error { color: red; font-size: 14px; margin-left: 10px; }
        .success { color: green; font-size: 16px; }
        form { width: 300px; margin: 20px auto; }
        input, textarea, select { width: 100%; padding: 8px; margin: 5px 0; }
    </style>
</head>
<body>

<h2>Contact Us</h2>

<?php if ($successMsg): ?>
    <p class="success"><?php echo $successMsg; ?></p>
<?php endif; ?>

<form method="post" action="">
    <label for="name">Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
    <span class="error"><?php echo $nameErr; ?></span>

    <br>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
    <span class="error"><?php echo $phoneErr; ?></span>

    <br>

    <label for="problem">Problem Description:</label>
    <textarea name="problem"><?php echo htmlspecialchars($problem); ?></textarea>
    <span class="error"><?php echo $problemErr; ?></span>

    <br>

    <label for="question">Question:</label>
    <select name="question">
        <option value="">Select a question</option>
        <option value="Boat not working" <?php if ($question == "Boat not working") echo "selected"; ?>>Boat not working</option>
        <option value="Need maintenance" <?php if ($question == "Need maintenance") echo "selected"; ?>>Need maintenance</option>
        <option value="Booking issue" <?php if ($question == "Booking issue") echo "selected"; ?>>Booking issue</option>
    </select>
    <span class="error"><?php echo $questionErr; ?></span>

    <br>

    <input type="submit" value="Submit">
</form>

</body>
</html>
