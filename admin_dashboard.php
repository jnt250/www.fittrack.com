<?php
session_start();

// Check if admin is logged in


// If the admin ID is not set, redirect to login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


require_once 'config.php';

// Function to get total number of users
function getTotalUsers($conn) {
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Function to get new users in the last 7 days
function getNewUsers($conn) {
    $sql = "SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['new_users'];
}

// Function to get active users in the last 30 days
function getActiveUsers($conn) {
    $sql = "SELECT COUNT(*) as active_users FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['active_users'];
}

// Function to get fitness goal distribution
function getFitnessGoalDistribution($conn) {
    $sql = "SELECT fitness_goal, COUNT(*) as count FROM users GROUP BY fitness_goal";
    $result = mysqli_query($conn, $sql);
    $goals = array();
    while($row = mysqli_fetch_assoc($result)) {
        $goals[$row['fitness_goal']] = $row['count'];
    }
    return $goals;
}

// Get dashboard data
$total_users = getTotalUsers($conn);
$new_users = getNewUsers($conn);
$active_users = getActiveUsers($conn);
$fitness_goals = getFitnessGoalDistribution($conn);

// Get latest users
$latest_users_sql = "SELECT user_id, full_name, username, email, fitness_goal, created_at, last_login 
                     FROM users ORDER BY created_at DESC LIMIT 5";
$latest_users_result = mysqli_query($conn, $latest_users_sql);

// Log the admin's dashboard access
$admin_id = $_SESSION["admin_id"];
$ip_address = $_SERVER['REMOTE_ADDR'];
$log_sql = "INSERT INTO admin_logs (admin_id, action, ip_address) VALUES (?, 'accessed dashboard', ?)";

$log_stmt = mysqli_prepare($conn, $log_sql);

if ($log_stmt === false) {
    // Log or show the error for debugging
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($log_stmt, "is", $admin_id, $ip_address);
mysqli_stmt_execute($log_stmt);
mysqli_stmt_close($log_stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitTrack</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }
        
        body {
            background-color: #f5f6fa;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--dark-bg);
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        
        .sidebar-header h2 {
            color: var(--primary-color);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            background-color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: var(--dark-bg);
            font-size: 24px;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
        }
        
        .admin-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .admin-info .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .admin-info .dropdown-btn {
            background: none;
            border: none;
            color: var(--dark-bg);
            font-weight: 500;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .admin-info .dropdown-btn i {
            margin-left: 5px;
        }
        
        .admin-info .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border-radius: 5px;
        }
        
        .admin-info .dropdown-content a {
            color: var(--dark-bg);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .admin-info .dropdown-content a:hover {
            background-color: var(--light-bg);
        }
        
        .admin-info .dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .card-icon {
            font-size: 30px;
            margin-bottom: 15px;
        }
        
        .card-total {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--dark-bg);
        }
        
        .card-label {
            color: var(--secondary-color);
            font-size: 16px;
        }
        
        .card-primary .card-icon {
            color: var(--primary-color);
        }
        
        .card-success .card-icon {
            color: var(--success-color);
        }
        
        .card-warning .card-icon {
            color: var(--warning-color);
        }
        
        .card-info .card-icon {
            color: var(--info-color);
        }
        
        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .chart-card {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .chart-header {
            margin-bottom: 15px;
        }
        
        .chart-header h3 {
            color: var(--dark-bg);
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .chart-header p {
            color: var(--secondary-color);
            font-size: 14px;
        }
        
        .chart-container {
            height: 300px;
            display: flex;
            align-items: flex-end;
            padding-top: 20px;
        }
        
        .bar {
            flex: 1;
            margin: 0 5px;
            background-color: var(--primary-color);
            border-radius: 5px 5px 0 0;
            position: relative;
            transition: height 0.5s;
        }
        
        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: var(--secondary-color);
        }
        
        .bar-value {
            position: absolute;
            top: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: var(--dark-bg);
        }
        
        /* Latest Users Table */
        .table-card {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .table-header {
            margin-bottom: 15px;
        }
        
        .table-header h3 {
            color: var(--dark-bg);
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .table-header p {
            color: var(--secondary-color);
            font-size: 14px;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th, .users-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .users-table th {
            color: var(--dark-bg);
            font-weight: 600;
            background-color: var(--light-bg);
        }
        
        .users-table tr:hover {
            background-color: rgba(74, 108, 247, 0.05);
        }
        
        .users-table .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--success-color);
        }
        
        .status-inactive {
            background-color: rgba(108, 117, 125, 0.2);
            color: var(--secondary-color);
        }
        
        .users-table .actions a {
            margin-right: 10px;
            color: var(--dark-bg);
            text-decoration: none;
        }
        
        .users-table .actions a:hover {
            color: var(--primary-color);
        }
        
        .view-all {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar-header h2 {
                display: none;
            }
            
            .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu i {
                margin-right: 0;
                font-size: 20px;
            }
            
            .main-content {
                margin-left: 80px;
            }
            
            .charts-section {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>FitTrack Admin</h2>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
                    <li><a href="user_analytics.php"><i class="fas fa-chart-line"></i> <span>User Analytics</span></a></li>
                 
                    <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Dashboard</h1>
                <div class="admin-info">
                   
                    <div class="dropdown">
                       
                        <div class="dropdown-content">
                            <a href="admin_profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card card-primary">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-total"><?php echo $total_users; ?></div>
                    <div class="card-label">Total Users</div>
                </div>
                <div class="card card-success">
                    <div class="card-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="card-total"><?php echo $new_users; ?></div>
                    <div class="card-label">New Users (Last 7 Days)</div>
                </div>
                <div class="card card-info">
                    <div class="card-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-total"><?php echo $active_users; ?></div>
                    <div class="card-label">Active Users (Last 30 Days)</div>
                </div>
                <div class="card card-warning">
                    <div class="card-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <div class="card-total"><?php echo count($fitness_goals); ?></div>
                    <div class="card-label">Fitness Goal Types</div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <!-- Fitness Goals Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Fitness Goal Distribution</h3>
                        <p>Number of users per fitness goal</p>
                    </div>
                    <div class="chart-container">
                        <?php
                        // Find maximum value for scaling
                        $max_value = 1; // Default to 1 to avoid division by zero
                        foreach ($fitness_goals as $goal => $count) {
                            if ($count > $max_value) {
                                $max_value = $count;
                            }
                        }
                        
                        // Display bars
                        foreach ($fitness_goals as $goal => $count) {
                            $height = ($count / $max_value) * 250; // Scale to chart height
                            $goal_display = ucwords(str_replace('_', ' ', $goal));
                            
                            echo "<div class='bar' style='height: {$height}px;'>";
                            echo "<div class='bar-value'>{$count}</div>";
                            echo "<div class='bar-label'>{$goal_display}</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                
                <!-- User Growth Chart (Placeholder) -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>User Growth</h3>
                        <p>New user registrations over time</p>
                    </div>
                    <div style="text-align: center; padding-top: 120px;">
                        <i class="fas fa-chart-line" style="font-size: 48px; color: #ddd;"></i>
                        <p style="color: #6c757d; margin-top: 15px;">User growth analytics will be displayed here</p>
                    </div>
                </div>
            </div>

            <!-- Latest Users Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3>Latest Users</h3>
                    <p>Most recently registered users</p>
                </div>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Fitness Goal</th>
                            <th>Registered</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($latest_users_result) > 0) {
                            while ($row = mysqli_fetch_assoc($latest_users_result)) {
                                $status_class = !empty($row['last_login']) && strtotime($row['last_login']) >= strtotime('-30 days') ? 'status-active' : 'status-inactive';
                                $status_text = !empty($row['last_login']) && strtotime($row['last_login']) >= strtotime('-30 days') ? 'Active' : 'Inactive';
                                $fitness_goal_display = ucwords(str_replace('_', ' ', $row['fitness_goal']));
                                
                                echo "<tr>";
                                echo "<td>" . $row['user_id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($fitness_goal_display) . "</td>";
                                echo "<td>" . date('M j, Y', strtotime($row['created_at'])) . "</td>";
                                echo "<td><span class='status {$status_class}'>{$status_text}</span></td>";
                                echo "<td class='actions'>";
                                echo "<a href='edit_user.php?id=" . $row['user_id'] . "' title='Edit'><i class='fas fa-edit'></i></a>";
                                echo "<a href='view_user.php?id=" . $row['user_id'] . "' title='View Details'><i class='fas fa-eye'></i></a>";
                                echo "<a href='delete_user.php?id=" . $row['user_id'] . "' title='Delete' onclick='return confirm(\"Are you sure you want to delete this user?\")'><i class='fas fa-trash-alt'></i></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align: center;'>No users found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <a href="manage_users.php" class="view-all">View All Users <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</body>
</html>