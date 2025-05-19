<?php
require_once 'config.php';

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit;
}

$user_id = $_GET['id'];

// Get user details
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt); // Get the result set
$user = mysqli_fetch_assoc($result);    // Use the result set here

if (!$user) {
    header("Location: manage_users.php");
    exit;
}

// Format fitness goal
$fitness_goal_display = ucwords(str_replace('_', ' ', $user['fitness_goal']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - FitTrack Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Your existing styles remain the same */
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'admin_header.php'; ?>

            <div class="user-details">
                <h2>User Details</h2>
                
                <div class="detail-row">
                    <div class="detail-label">User ID:</div>
                    <div class="detail-value"><?php echo $user['user_id']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Full Name:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Username:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['username']); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Fitness Goal:</div>
                    <div class="detail-value"><?php echo $fitness_goal_display; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Date Registered:</div>
                    <div class="detail-value"><?php echo date('M j, Y H:i', strtotime($user['created_at'])); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Last Login:</div>
                    <div class="detail-value">
                        <?php echo $user['last_login'] ? date('M j, Y H:i', strtotime($user['last_login'])) : 'Never logged in'; ?>
                    </div>
                </div>
                
                <a href="manage_users.php" class="back-btn">Back to Users</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>