<?php
// This is a reusable sidebar component that can be included in other files
?>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>FitTrack Admin</h2>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li><a href="admin_dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a></li>
            <li><a href="manage_users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-users"></i> <span>Manage Users</span>
            </a></li>
            <li><a href="user_analytics.php" <?php echo basename($_SERVER['PHP_SELF']) == 'user_analytics.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-chart-line"></i> <span>User Analytics</span>
            </a></li>
            <li><a href="admin_settings.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_settings.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a></li>
            <li><a href="admin_logout.php">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a></li>
        </ul>
    </div>
</div>