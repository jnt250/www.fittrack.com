<?php
session_start();
require_once 'config.php';

// Check if admin is logged in


// Initialize arrays to prevent undefined variable errors
$registration_data = [];
$activity_data = [];
$fitness_goals = [];
$device_data = [];
$retention_data = ['retained_users' => 0];

// Get user registration by month
$registration_sql = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month, 
                        COUNT(*) as count 
                     FROM users 
                     GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                     ORDER BY month";
$registration_result = mysqli_query($conn, $registration_sql);

if ($registration_result) {
    while ($row = mysqli_fetch_assoc($registration_result)) {
        $registration_data[$row['month']] = $row['count'];
    }
} else {
    error_log("Registration query failed: " . mysqli_error($conn));
}

// Get user activity data
$activity_sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN last_login < DATE_SUB(NOW(), INTERVAL 30 DAY) OR last_login IS NULL THEN 1 ELSE 0 END) as inactive_users
                 FROM users";
$activity_result = mysqli_query($conn, $activity_sql);

if ($activity_result && mysqli_num_rows($activity_result) > 0) {
    $activity_data = mysqli_fetch_assoc($activity_result);
} else {
    error_log("Activity query failed: " . mysqli_error($conn));
    $activity_data = [
        'total_users' => 0,
        'active_users' => 0,
        'inactive_users' => 0
    ];
}

// Get fitness goal distribution
$fitness_goals_sql = "SELECT fitness_goal, COUNT(*) as count FROM users GROUP BY fitness_goal";
$fitness_goals_result = mysqli_query($conn, $fitness_goals_sql);

if ($fitness_goals_result) {
    while ($row = mysqli_fetch_assoc($fitness_goals_result)) {
        $fitness_goals[$row['fitness_goal']] = $row['count'];
    }
} else {
    error_log("Fitness goals query failed: " . mysqli_error($conn));
}

// Get device usage data - only if table exists
$device_sql = "SHOW TABLES LIKE 'user_devices'";
$table_check = mysqli_query($conn, $device_sql);

if ($table_check && mysqli_num_rows($table_check) > 0) {
    $device_sql = "SELECT device_type, COUNT(*) as count FROM user_devices GROUP BY device_type";
    $device_result = mysqli_query($conn, $device_sql);
    
    if ($device_result) {
        while ($row = mysqli_fetch_assoc($device_result)) {
            $device_data[$row['device_type']] = $row['count'];
        }
    }
}

// Get user retention data - only if table exists
$retention_sql = "SHOW TABLES LIKE 'user_sessions'";
$table_check = mysqli_query($conn, $retention_sql);

if ($table_check && mysqli_num_rows($table_check) > 0) {
    $retention_sql = "SELECT COUNT(DISTINCT user_id) as retained_users 
                      FROM user_sessions 
                      WHERE session_date BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()";
    $retention_result = mysqli_query($conn, $retention_sql);
    
    if ($retention_result && mysqli_num_rows($retention_result) > 0) {
        $retention_data = mysqli_fetch_assoc($retention_result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitTrack Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
            --border-color: #dee2e6;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--dark-bg);
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.2);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h2 {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 600;
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
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .sidebar-menu i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        /* Header Styles */
        .header {
            background-color: #fff;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: var(--dark-bg);
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        /* Admin Info Dropdown */
        .admin-info {
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .admin-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        
        .dropdown {
            position: relative;
        }
        
        .dropdown-btn {
            background: none;
            border: none;
            color: var(--dark-bg);
            font-weight: 500;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .dropdown-btn:hover {
            background-color: var(--light-bg);
        }
        
        .dropdown-btn i {
            margin-left: 8px;
            font-size: 0.9rem;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .dropdown-content a {
            color: var(--dark-bg);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .dropdown-content a:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* Analytics Container */
        .analytics-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.95rem;
            color: var(--secondary-color);
        }
        
        .card-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(74, 108, 247, 0.1);
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        /* Chart Cards */
        .chart-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-header h3 {
            font-size: 1.2rem;
            color: var(--dark-bg);
            font-weight: 600;
        }
        
        .card-body {
            height: 300px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }
            
            .sidebar-header h2 {
                display: none;
            }
            
            .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .analytics-container {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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
                    <li><a href="user_analytics.php"><i class="fas fa-chart-line"></i> <span>Analytics</span></a></li>
                   
                    <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Dashboard Overview</h1>
               
            </div>

            <!-- Analytics Cards -->
            <div class="analytics-container">
                <div class="stat-card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo isset($activity_data['total_users']) ? number_format($activity_data['total_users']) : 0; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                
                <div class="stat-card">
                    <div class="card-icon" style="color: var(--success-color); background-color: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-value" style="color: var(--success-color);"><?php echo isset($activity_data['active_users']) ? number_format($activity_data['active_users']) : 0; ?></div>
                    <div class="stat-label">Active Users</div>
                </div>
                
                <div class="stat-card">
                    <div class="card-icon" style="color: var(--secondary-color); background-color: rgba(108, 117, 125, 0.1);">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-value" style="color: var(--secondary-color);"><?php echo isset($activity_data['inactive_users']) ? number_format($activity_data['inactive_users']) : 0; ?></div>
                    <div class="stat-label">Inactive Users</div>
                </div>
                
                <div class="stat-card">
                    <div class="card-icon" style="color: var(--info-color); background-color: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-value" style="color: var(--info-color);"><?php echo isset($retention_data['retained_users']) ? number_format($retention_data['retained_users']) : 0; ?></div>
                    <div class="stat-label">Retained Users (7d)</div>
                </div>
            </div>

            <!-- Registration Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h3>User Registrations</h3>
                </div>
                <div class="card-body">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>

            <!-- Fitness Goals Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h3>Fitness Goals Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="goalsChart"></canvas>
                </div>
            </div>

            <!-- Device Usage Chart (if data exists) -->
            <?php if (!empty($device_data)): ?>
            <div class="chart-card">
                <div class="card-header">
                    <h3>Device Usage</h3>
                </div>
                <div class="card-body">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Registration Chart
        const registrationCtx = document.getElementById('registrationChart').getContext('2d');
        const registrationChart = new Chart(registrationCtx, {
            type: 'line',
            data: {
                labels: <?php echo !empty($registration_data) ? json_encode(array_keys($registration_data)) : json_encode([]); ?>,
                datasets: [{
                    label: 'User Registrations',
                    data: <?php echo !empty($registration_data) ? json_encode(array_values($registration_data)) : json_encode([]); ?>,
                    backgroundColor: 'rgba(74, 108, 247, 0.1)',
                    borderColor: 'rgba(74, 108, 247, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Fitness Goals Chart
        const goalsCtx = document.getElementById('goalsChart').getContext('2d');
        const goalsChart = new Chart(goalsCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo !empty($fitness_goals) ? json_encode(array_map(function($key) { 
                    return ucwords(str_replace('_', ' ', $key)); 
                }, array_keys($fitness_goals))) : json_encode([]); ?>,
                datasets: [{
                    data: <?php echo !empty($fitness_goals) ? json_encode(array_values($fitness_goals)) : json_encode([]); ?>,
                    backgroundColor: [
                        'rgba(74, 108, 247, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                cutout: '70%'
            }
        });

        <?php if (!empty($device_data)): ?>
        // Device Usage Chart
        const deviceCtx = document.getElementById('deviceChart').getContext('2d');
        const deviceChart = new Chart(deviceCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($device_data)); ?>,
                datasets: [{
                    label: 'Device Usage',
                    data: <?php echo json_encode(array_values($device_data)); ?>,
                    backgroundColor: 'rgba(74, 108, 247, 0.8)',
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>