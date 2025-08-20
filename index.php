<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
</head>
<body>
    <?php if (!isset($_SESSION['user_id'])): ?> 
        <p>Don't have an account? <a href="register.php">Register</a></p>
    <?php else: ?>
        <p>Welcome, <?php echo $_SESSION['user_name']; ?> | <a href="logout.php">Logout</a></p>
    <?php endif; ?>
</body>
</html>
