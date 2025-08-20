<?php
ini_set('session.gc_maxlifetime', 3600); // 1-hour session lifetime
session_set_cookie_params(3600); // 1-hour session cookie
session_start();

// ✅ Database Connection
$host = "localhost";
$username = "root";
$password = "";
$database = "seabooker";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("DEBUG: Connection failed: " . $conn->connect_error);
}

// ✅ Get Action (signin/login)
$action = isset($_POST['action']) ? trim($_POST['action']) : "";

if ($action == "signin") {
    // ✅ Sanitize & Validate Inputs
    $owner_name = trim($_POST['owner_name']);
    $email = strtolower(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!preg_match("/^[a-zA-Z ]+$/", $owner_name)) die("Error: Owner name should only contain letters.");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, "@gmail.com")) die("Error: Email must be a valid Gmail address.");

    // ✅ Hash Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Check for Duplicate Email
    $check_email = $conn->prepare($query = "SELECT id, owner_name, password FROM boat_owners WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        die("Error: Email already registered. Use a different email.");
    }
    $check_email->close();

    // ✅ Insert into Database
    $stmt = $conn->prepare("INSERT INTO boat_owners (owner_name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $owner_name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Sign-In successful! Redirecting to login...'); window.location.href='admin.html';</script>";
    } else {
        echo "Error inserting data: " . $stmt->error;
    }
    $stmt->close();
} elseif ($action == "login") {
    // ✅ Login Handling
    $email = strtolower(trim($_POST['email']));
    $password = trim($_POST['password']);

    $query = "SELECT id, owner_name, password FROM boat_owners WHERE LOWER(email) = LOWER(?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($admin_id, $admin_name, $hashed_password);

    if ($stmt->num_rows == 1) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // ✅ Store in Session
            $_SESSION["admin_id"] = $admin_id;
            $_SESSION["admin_name"] = $admin_name;
            $_SESSION["email"] = $email;

            echo "<script>alert('Login successful! Redirecting to dashboard...'); window.location.href='admin_dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid Password'); window.location.href='admin.html';</script>";
        }
    } else {
        echo "<script>alert('Admin not found'); window.location.href='admin.html';</script>";
    }
    $stmt->close();
}

$conn->close();
?>