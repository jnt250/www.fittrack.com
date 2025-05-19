<?php
$password = "123456"; // Change to your desired admin password
$hashed = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hashed;
?>
