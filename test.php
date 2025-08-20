<?php
$entered_password = "1919";
$stored_hashed_password = "$2y$10$8anGiPBoMJYBfbiua0seRe9I/OXnjaOH4OWagFVEmFdntk8crTLvy"; // Paste from the database

if (password_verify($entered_password, $stored_hashed_password)) {
    echo "Password MATCHES!";
} else {
    echo "Password DOES NOT match!";
}
?>
