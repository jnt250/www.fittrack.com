<?php
require_once 'config.php';

// Pagination variables
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of users
$total_users_sql = "SELECT COUNT(*) as total FROM users";
$total_users_result = mysqli_query($conn, $total_users_sql);
$total_users_row = mysqli_fetch_assoc($total_users_result);
$total_users = $total_users_row['total'];
$total_pages = ceil($total_users / $records_per_page);

// Get users with pagination
$users_sql = "SELECT user_id, full_name, username, email, fitness_goal, created_at, last_login 
              FROM users ORDER BY created_at DESC LIMIT $offset, $records_per_page";
$users_result = mysqli_query($conn, $users_sql);

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    $users_sql = "SELECT user_id, full_name, username, email, fitness_goal, created_at, last_login 
                  FROM users 
                  WHERE username LIKE '%$search%' OR email LIKE '%$search%' OR full_name LIKE '%$search%'
                  ORDER BY created_at DESC";
    $users_result = mysqli_query($conn, $users_sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FitTrack Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
            --border-color: #dee2e6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f6fa;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles - Matches Admin Dashboard */
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
        
        /* Header Styles - Matches Admin Dashboard */
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
        
        /* Table Styles */
        .table-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .table-header {
            margin-bottom: 1.5rem;
        }
        
        .table-header h3 {
            color: var(--dark-bg);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .table-header p {
            color: var(--secondary-color);
            font-size: 0.875rem;
        }
        
        .search-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.15s ease-in-out;
        }
        
        .search-box:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
        }
        
        .search-btn, .add-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .search-btn {
            background-color: var(--primary-color);
            color: white;
            border: 1px solid var(--primary-color);
        }
        
        .search-btn:hover {
            background-color: #3a5bd9;
            border-color: #3a5bd9;
        }
        
        .add-btn {
            background-color: var(--success-color);
            color: white;
            border: 1px solid var(--success-color);
            text-decoration: none;
        }
        
        .add-btn:hover {
            background-color: #218838;
            border-color: #218838;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        
        .users-table th, .users-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .users-table th {
            background-color: var(--light-bg);
            color: var(--dark-bg);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .users-table tr:hover {
            background-color: rgba(74, 108, 247, 0.05);
        }
        
        .status {
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-active {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--success-color);
        }
        
        .status-inactive {
            background-color: rgba(108, 117, 125, 0.2);
            color: var(--secondary-color);
        }
        
        .actions {
            display: flex;
            gap: 0.75rem;
        }
        
        .actions a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.15s ease-in-out;
            font-size: 0.875rem;
        }
        
        .actions a:hover {
            color: var(--primary-color);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            gap: 0.5rem;
        }
        
        .pagination a {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.15s ease-in-out;
        }
        
        .pagination a:hover {
            background-color: var(--light-bg);
        }
        
        .pagination a.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .no-users {
            text-align: center;
            padding: 2rem;
            color: var(--secondary-color);
        }
        
        /* Admin Info Dropdown - Matches Admin Dashboard */
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
        
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-btn {
            background: none;
            border: none;
            color: var(--dark-bg);
            font-weight: 500;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .dropdown-btn i {
            margin-left: 5px;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border-radius: 5px;
        }
        
        .dropdown-content a {
            color: var(--dark-bg);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .dropdown-content a:hover {
            background-color: var(--light-bg);
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
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
        }
        
        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
            }
            
            .users-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar - Matches Admin Dashboard -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>FitTrack Admin</h2>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                    <li><a href="manage_users.php" class="active"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
                    <li><a href="user_analytics.php"><i class="fas fa-chart-line"></i> <span>User Analytics</span></a></li>
                    
                    <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header - Matches Admin Dashboard -->
            

            <div class="table-card">
                <div class="table-header">
                    <h3>User Management</h3>
                    <p>View and manage all registered users</p>
                </div>
                
                <!-- Search Box -->
                <div class="search-container">
                    <form method="GET" action="manage_users.php" class="search-form" style="display: flex; gap: 0.5rem; flex: 1;">
                        <input type="text" name="search" class="search-box" placeholder="Search by name, username or email" 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <?php if (isset($_GET['search'])): ?>
                            <a href="manage_users.php" class="search-btn" style="background-color: var(--secondary-color); border-color: var(--secondary-color);">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        <?php endif; ?>
                    </form>
                    <a href="add_user.php" class="add-btn">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
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
                        <?php if (mysqli_num_rows($users_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($users_result)): ?>
                                <?php
                                $status_class = !empty($row['last_login']) && strtotime($row['last_login']) >= strtotime('-30 days') ? 'status-active' : 'status-inactive';
                                $status_text = !empty($row['last_login']) && strtotime($row['last_login']) >= strtotime('-30 days') ? 'Active' : 'Inactive';
                                $fitness_goal_display = ucwords(str_replace('_', ' ', $row['fitness_goal']));
                                ?>
                                <tr>
                                    <td><?php echo $row['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($fitness_goal_display); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                    <td><span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                    <td class="actions">
                                        <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view_user.php?id=<?php echo $row['user_id']; ?>" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="no-users">
                                    <i class="fas fa-user-slash" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                    <p>No users found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if (!isset($_GET['search']) && $total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="manage_users.php?page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php 
                    // Show limited pagination links
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    
                    if ($start > 1) {
                        echo '<a href="manage_users.php?page=1">1</a>';
                        if ($start > 2) echo '<span>...</span>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++): ?>
                        <a href="manage_users.php?page=<?php echo $i; ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    if ($end < $total_pages) {
                        if ($end < $total_pages - 1) echo '<span>...</span>';
                        echo '<a href="manage_users.php?page='.$total_pages.'">'.$total_pages.'</a>';
                    }
                    ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="manage_users.php?page=<?php echo $page + 1; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>