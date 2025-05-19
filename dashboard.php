<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FitTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Global styles */
        :root {
          --primary: #2563eb;
          --primary-dark: #1e40af;
          --primary-light: #dbeafe;
          --secondary: #4f46e5;
          --accent: #8b5cf6;
          --dark: #1e293b;
          --light: #f8fafc;
          --gray-100: #f1f5f9;
          --gray-200: #e2e8f0;
          --gray-300: #cbd5e0;
          --gray-400: #94a3b8;
          --gray-500: #64748b;
          --success: #10b981;
          --warning: #f59e0b;
          --danger: #ef4444;
          --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
          --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
          --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
          --radius: 0.5rem;
        }
        
        body {
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
          background-color: #f9fafb;
          color: var(--dark);
          line-height: 1.5;
          margin: 0;
          padding: 0;
        }
        
        /* Header and navigation */
        header {
          background-color: white;
          box-shadow: var(--shadow);
          position: sticky;
          top: 0;
          z-index: 100;
        }
        
        nav {
          max-width: 1400px;
          margin: 0 auto;
          padding: 0.75rem 1.5rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }
        
        .logo {
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }
        
        .logo img {
          height: 40px;
          width: auto;
        }
        
        .logo h1 {
          font-size: 1.5rem;
          font-weight: 700;
          margin: 0;
          color: var(--primary);
          letter-spacing: -0.025em;
        }
        
        nav ul {
          display: flex;
          gap: 0.5rem;
          list-style: none;
          margin: 0;
          padding: 0;
        }
        
        nav ul li a {
          display: flex;
          align-items: center;
          padding: 0.75rem 1rem;
          color: var(--gray-500);
          text-decoration: none;
          font-weight: 500;
          border-radius: var(--radius);
          transition: all 0.2s ease;
        }
        
        nav ul li a:hover {
          color: var(--primary);
          background-color: var(--primary-light);
        }
        
        nav ul li a.active {
          color: white;
          background-color: var(--primary);
        }
        
        nav ul li a i {
          margin-right: 0.5rem;
          font-size: 1.125rem;
        }
        
        .logout-btn {
          background-color: var(--gray-100);
          color: var(--gray-500);
        }
        
        .logout-btn:hover {
          background-color: var(--danger);
          color: white;
        }
        
        /* Dashboard container */
        .dashboard-container {
          max-width: 1400px;
          margin: 1.5rem auto 3rem;
          padding: 0 1.5rem;
        }
        
        /* Welcome banner */
        .welcome-banner {
          background: linear-gradient(135deg, var(--primary), var(--secondary));
          color: white;
          padding: 2.5rem 2rem;
          border-radius: var(--radius);
          margin-bottom: 2rem;
          text-align: center;
          box-shadow: var(--shadow-md);
          position: relative;
          overflow: hidden;
        }
        
        .welcome-banner::after {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImciIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPjxzdG9wIG9mZnNldD0iMCUiIHN0b3AtY29sb3I9IiNmZmZmZmYiIHN0b3Atb3BhY2l0eT0iMC4xIiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIwIiAvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxwYXRoIGQ9Ik0wIDgwQzQwIDgwIDQ1IDIwIDEwMCAwbDAgMTAwSDBWMHoiIGZpbGw9InVybCgjZykiLz48L3N2Zz4=');
          opacity: 0.1;
        }
        
        .welcome-banner h2 {
          font-size: 2.25rem;
          font-weight: 700;
          margin-bottom: 0.75rem;
          letter-spacing: -0.025em;
        }
        
        .welcome-banner p {
          font-size: 1.125rem;
          margin: 0;
          opacity: 0.9;
        }
        
        /* Dashboard grid */
        .dashboard-grid {
          display: grid;
          grid-template-columns: repeat(12, 1fr);
          gap: 1.5rem;
        }
        
        .dashboard-grid .dashboard-card:nth-child(1) {
          grid-column: span 3;
        }
        
        .dashboard-grid .dashboard-card:nth-child(2) {
          grid-column: span 3;
        }
        
        .dashboard-grid .dashboard-card:nth-child(3) {
          grid-column: span 6;
        }
        
        .dashboard-grid .dashboard-card:nth-child(4) {
          grid-column: span 12;
        }
        
        /* Dashboard cards */
        .dashboard-card {
          background-color: white;
          border-radius: var(--radius);
          box-shadow: var(--shadow);
          padding: 1.5rem;
          transition: all 0.3s ease;
          height: 100%;
          display: flex;
          flex-direction: column;
        }
        
        .dashboard-card:hover {
          box-shadow: var(--shadow-md);
          transform: translateY(-4px);
        }
        
        .dashboard-card h3 {
          color: var(--dark);
          font-size: 1.25rem;
          font-weight: 600;
          margin-top: 0;
          margin-bottom: 1.25rem;
          padding-bottom: 0.75rem;
          border-bottom: 1px solid var(--gray-200);
          position: relative;
        }
        
        .dashboard-card h3::after {
          content: '';
          position: absolute;
          bottom: -1px;
          left: 0;
          width: 50px;
          height: 3px;
          background-color: var(--primary);
          border-radius: 1.5px;
        }
        
        /* User profile card */
        .user-details {
          display: flex;
          align-items: center;
          margin-bottom: 1.5rem;
        }
        
        .user-avatar {
          width: 70px;
          height: 70px;
          border-radius: 50%;
          background-color: var(--primary-light);
          display: flex;
          align-items: center;
          justify-content: center;
          margin-right: 1.25rem;
          flex-shrink: 0;
        }
        
        .user-avatar i {
          font-size: 2.5rem;
          color: var(--primary);
        }
        
        .user-stats {
          flex-grow: 1;
        }
        
        .user-stats p {
          margin: 0.5rem 0;
          color: var(--gray-500);
          font-size: 0.9375rem;
        }
        
        .user-stats p strong {
          color: var(--dark);
          font-weight: 600;
          margin-right: 0.25rem;
        }
        
        /* Quick form */
        .quick-form .form-group {
          margin-bottom: 1.25rem;
        }
        
        .quick-form label {
          display: block;
          margin-bottom: 0.5rem;
          color: var(--gray-500);
          font-size: 0.9375rem;
          font-weight: 500;
        }
        
        .quick-form input,
        .quick-form select {
          width: 100%;
          padding: 0.75rem;
          border: 1px solid var(--gray-300);
          border-radius: var(--radius);
          color: var(--dark);
          background-color: var(--light);
          transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        
        .quick-form input:focus,
        .quick-form select:focus {
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
          outline: none;
        }
        
        /* Buttons */
        .dashboard-btn {
          display: inline-block;
          background-color: var(--primary);
          color: white;
          padding: 0.75rem 1.5rem;
          border-radius: var(--radius);
          border: none;
          cursor: pointer;
          font-weight: 600;
          text-decoration: none;
          transition: all 0.2s ease;
          text-align: center;
          margin-top: auto;
        }
        
        .dashboard-btn:hover {
          background-color: var(--primary-dark);
          transform: translateY(-2px);
        }
        
        /* Progress chart */
        .progress-chart {
          height: 240px;
          margin-bottom: 1.25rem;
        }
        
        .progress-note {
          text-align: center;
          color: var(--gray-400);
          font-size: 0.875rem;
        }
        
        /* Workout list */
        .workout-list {
          list-style: none;
          padding: 0;
          margin: 0 0 1.5rem;
        }
        
        .workout-list li {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 1rem 0;
          border-bottom: 1px solid var(--gray-200);
        }
        
        .workout-list li:last-child {
          border-bottom: none;
        }
        
        .workout-info {
          display: flex;
          align-items: center;
        }
        
        .workout-info i {
          font-size: 1.5rem;
          color: var(--primary);
          background-color: var(--primary-light);
          padding: 0.75rem;
          border-radius: 50%;
          margin-right: 1rem;
          flex-shrink: 0;
        }
        
        .workout-info h4 {
          margin: 0;
          font-size: 1rem;
          font-weight: 600;
          color: var(--dark);
        }
        
        .workout-info p {
          margin: 0.25rem 0 0;
          font-size: 0.875rem;
          color: var(--gray-400);
        }
        
        .reminder-btn {
          background: none;
          border: 1px solid var(--gray-300);
          border-radius: 50%;
          width: 36px;
          height: 36px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1rem;
          color: var(--gray-400);
          cursor: pointer;
          transition: all 0.2s ease;
        }
        
        .reminder-btn:hover {
          color: var(--primary);
          border-color: var(--primary);
          background-color: var(--primary-light);
        }
        
        /* Footer */
        footer {
          background-color: var(--dark);
          color: white;
          padding: 3rem 1.5rem 1.5rem;
        }
        
        .footer-content {
          max-width: 1400px;
          margin: 0 auto;
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 2rem;
        }
        
        .footer-logo {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          margin-bottom: 1rem;
        }
        
        .footer-logo img {
          height: 36px;
          width: auto;
        }
        
        .footer-logo h2 {
          margin: 0;
          font-size: 1.5rem;
          font-weight: 700;
        }
        
        .footer-links h3,
        .footer-social h3 {
          font-size: 1.125rem;
          margin-top: 0;
          margin-bottom: 1.25rem;
          color: var(--gray-300);
        }
        
        .footer-links ul {
          list-style: none;
          padding: 0;
          margin: 0;
        }
        
        .footer-links ul li {
          margin-bottom: 0.75rem;
        }
        
        .footer-links ul li a {
          color: var(--gray-400);
          text-decoration: none;
          transition: color 0.2s ease;
        }
        
        .footer-links ul li a:hover {
          color: white;
        }
        
        .social-icons {
          display: flex;
          gap: 1rem;
        }
        
        .social-icons a {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
          background-color: rgba(255, 255, 255, 0.1);
          border-radius: 50%;
          color: white;
          transition: all 0.2s ease;
        }
        
        .social-icons a:hover {
          background-color: var(--primary);
          transform: translateY(-3px);
        }
        
        .footer-bottom {
          max-width: 1400px;
          margin: 2rem auto 0;
          padding-top: 1.5rem;
          border-top: 1px solid rgba(255, 255, 255, 0.1);
          text-align: center;
          color: var(--gray-400);
          font-size: 0.875rem;
        }
        
        /* Responsive design */
        @media (max-width: 1200px) {
          .dashboard-grid .dashboard-card:nth-child(1),
          .dashboard-grid .dashboard-card:nth-child(2) {
            grid-column: span 6;
          }
          
          .dashboard-grid .dashboard-card:nth-child(3) {
            grid-column: span 12;
          }
        }
        
        @media (max-width: 768px) {
          nav {
            flex-direction: column;
            padding: 1rem;
          }
          
          .logo {
            margin-bottom: 1rem;
          }
          
          nav ul {
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
          }
          
          .dashboard-grid .dashboard-card:nth-child(1),
          .dashboard-grid .dashboard-card:nth-child(2),
          .dashboard-grid .dashboard-card:nth-child(3),
          .dashboard-grid .dashboard-card:nth-child(4) {
            grid-column: span 12;
          }
          
          .user-details {
            flex-direction: column;
            text-align: center;
          }
          
          .user-avatar {
            margin-right: 0;
            margin-bottom: 1rem;
          }
          
          .footer-content {
            grid-template-columns: 1fr;
            text-align: center;
          }
          
          .footer-logo {
            justify-content: center;
          }
          
          .social-icons {
            justify-content: center;
          }
        }
        
        @media (max-width: 576px) {
          .welcome-banner {
            padding: 2rem 1.5rem;
          }
          
          .welcome-banner h2 {
            font-size: 1.75rem;
          }
          
          nav ul li a {
            padding: 0.5rem 0.75rem;
            font-size: 0.9375rem;
          }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="logo.png" alt="FitTrack Logo">
                <h1>FitTrack</h1>
            </div>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                <li><a href="workouts.html"><i class="fas fa-dumbbell"></i>Workouts</a></li>
                <li><a href="nutrition.hml"><i class="fas fa-apple-alt"></i>Nutrition</a></li>
                <li><a href="profile.html"><i class="fas fa-user"></i>Profile</a></li>
                <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="dashboard-container">
        <div class="welcome-banner">
            <h2>Welcome back, John!</h2>
            <p>Your fitness journey continues today</p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card user-info">
                <h3>Your Profile</h3>
                <div class="user-details">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-stats">
                        <p><strong>Goal:</strong> Weight Loss</p>
                        <p><strong>Height:</strong> 175 cm</p>
                        <p><strong>Weight:</strong> 80 kg</p>
                    </div>
                </div>
                <a href="profile.html" class="dashboard-btn">Update Profile</a>
            </div>

            <div class="dashboard-card quick-log">
                <h3>Quick Activity Log</h3>
                <form action="log_activity.php" method="POST" class="quick-form">
                    <div class="form-group">
                        <label for="activity_type">Activity Type</label>
                        <select id="activity_type" name="activity_type">
                            <option value="walking">Walking</option>
                            <option value="running">Running</option>
                            <option value="cycling">Cycling</option>
                            <option value="swimming">Swimming</option>
                            <option value="gym">Gym Workout</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" min="1">
                    </div>
                    <button type="submit" class="dashboard-btn">Log Activity</button>
                </form>
            </div>

            <div class="dashboard-card progress">
                <h3>Weekly Progress</h3>
                <div class="progress-chart">
                    <canvas id="progressChart"></canvas>
                </div>
                <p class="progress-note">Track your weekly activity to see your progress here.</p>
            </div>

            <div class="dashboard-card upcoming">
                <h3>Upcoming Workouts</h3>
                <ul class="workout-list">
                    <li>
                        <div class="workout-info">
                            <i class="fas fa-running"></i>
                            <div>
                                <h4>HIIT Training</h4>
                                <p>Tomorrow, 6:00 AM</p>
                            </div>
                        </div>
                        <button class="reminder-btn"><i class="far fa-bell"></i></button>
                    </li>
                    <li>
                        <div class="workout-info">
                            <i class="fas fa-dumbbell"></i>
                            <div>
                                <h4>Strength Training</h4>
                                <p>Wednesday, 5:30 PM</p>
                            </div>
                        </div>
                        <button class="reminder-btn"><i class="far fa-bell"></i></button>
                    </li>
                    <li>
                        <div class="workout-info">
                            <i class="fas fa-swimmer"></i>
                            <div>
                                <h4>Swimming Session</h4>
                                <p>Friday, 7:00 AM</p>
                            </div>
                        </div>
                        <button class="reminder-btn"><i class="far fa-bell"></i></button>
                    </li>
                </ul>
                <a href="workouts.html" class="dashboard-btn">View All Workouts</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <img src="logo.png" alt="FitTrack Logo">
                <h2>FitTrack</h2>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="workouts.php">Workouts</a></li>
                    <li><a href="nutrition.php">Nutrition</a></li>
                    <li><a href="profile.php">Profile</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h3>Connect With Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 FitTrack. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>
        // Sample chart data - this would be replaced with real user data
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Activity (minutes)',
                    data: [30, 45, 0, 60, 20, 90, 45],
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>