<?php
session_start();
require_once 'config.php';

// Check if admin is logged in


// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit;
}

$user_id = $_GET['id'];

// Delete user
$sql = "DELETE FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

// Redirect back to manage users page
header("Location: manage_users.php?deleted=1");
exit;
?>