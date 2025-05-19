<?php
$entered_password = '123456';
$stored_hash = '$2y$10$IlkjNRKWq4iz0XtOy1IljehBaIVIqrWjiWyLgkwA8t0gvaReCK/o6'; // example from your DB

if (password_verify($entered_password, $stored_hash)) {
    echo "✅ Password is correct!";
} else {
    echo "❌ Password is incorrect!";
}
?>
