<?php
session_start();
require_once 'config.php';

// Check admin authentication
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.html");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $delete_sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: admin_kaka.php");
    exit;
}

// Get users
$users_sql = "SELECT user_id, full_name, username, email, fitness_goal, created_at FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin User Management</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <h2>Admin Panel - Manage Users</h2>
    <a href="create_user.php">+ Create New User</a>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Fitness Goal</th>
                <th>Registered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($users_result)): ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['fitness_goal']) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['user_id'] ?>">Edit</a> |
                    <a href="admin_kaka.php?delete=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
