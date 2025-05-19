


<?php
// Database Configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Change to your MySQL username
define('DB_PASSWORD', ''); // Change to your MySQL password
define('DB_NAME', 'fittrack');

// Attempt to connect to the database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn === false) {
    die("ERROR: Could not connect to the database. " . mysqli_connect_error());
}

// Set character set
mysqli_set_charset($conn, "utf8mb4");

// ✅ Fix session warning by checking if session already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
