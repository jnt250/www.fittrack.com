<?php
session_start();
require_once 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username or email.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        // First: Check if it's an admin
        $admin_sql = "SELECT admin_id, username, email, password FROM admins WHERE username = ? OR email = ?";
        if ($stmt = mysqli_prepare($conn, $admin_sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $admin_id, $admin_username, $admin_email, $admin_hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $admin_hashed_password)) {
                        // Admin login success
                        $_SESSION["loggedin"] = true;
                        $_SESSION["admin_id"] = $admin_id;
                        $_SESSION["username"] = $admin_username;
                        header("Location: admin_dashboard.php");
                        exit;
                    } else {
                        $login_err = "Wrong username or password. Please try again.";
                    }
                }
                mysqli_stmt_close($stmt);
            } else {
                // Second: Check regular users
                $user_sql = "SELECT user_id, username, email, password FROM users WHERE username = ? OR email = ?";
                if ($stmt = mysqli_prepare($conn, $user_sql)) {
                    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_store_result($stmt);
                        if (mysqli_stmt_num_rows($stmt) == 1) {
                            mysqli_stmt_bind_result($stmt, $user_id, $db_username, $db_email, $hashed_password);
                            if (mysqli_stmt_fetch($stmt)) {
                                if (password_verify($password, $hashed_password)) {
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["user_id"] = $user_id;
                                    $_SESSION["username"] = $db_username;

                                    $update_sql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
                                    if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                                        mysqli_stmt_bind_param($update_stmt, "i", $user_id);
                                        mysqli_stmt_execute($update_stmt);
                                        mysqli_stmt_close($update_stmt);
                                    }

                                    header("Location: dashboard.php");
                                    exit;
                                } else {
                                    $login_err = "Wrong username or password. Please try again.";
                                }
                            }
                        } else {
                            $login_err = "Wrong username or password. Please try again.";
                        }
                    } else {
                        $login_err = "Oops! Something went wrong. Please try again later.";
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitTrack</title>
    <link rel="stylesheet" href="css style.css">
    <link rel="stylesheet" href="css auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #dc3545;
            border-radius: 5px;
            background-color: #f8d7da;
            text-align: center;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #28a745;
            border-radius: 5px;
            background-color: #d4edda;
            text-align: center;
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
                <li><a href="index.html">Home</a></li>
                <li><a href="signup.html" class="signup-btn">Sign Up</a></li>
            </ul>
        </nav>
    </header>
    
    <section class="auth-container">
        <div class="auth-image">
            <img src="Homepage.png" alt="Fitness Login">
        </div>
        <div class="auth-form">
            <h2>Welcome Back!</h2>
            <p>Log in to continue your fitness journey</p>
            
            <?php
            // Check for success parameter from registration
            if (isset($_GET["registration"]) && $_GET["registration"] == "success") {
                echo '<div class="success-message">Registration successful! Please log in.</div>';
            }
            
            // Display login error if any
            if (!empty($login_err)) {
                echo '<div class="error-message">' . $login_err . '</div>';
            }
            
            // Display validation errors
            if (!empty($username_err) || !empty($password_err)) {
                echo '<div class="error-message">';
                if (!empty($username_err)) echo $username_err . '<br>'; 
                if (!empty($password_err)) echo $password_err;
                echo '</div>';
            }
            ?>
            
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
                </div>
                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit" class="auth-btn">Login</button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.html">Sign up</a></p>
            </div>
        </div>
    </section>
    
    <footer>
        <div class="footer-content">
            <div class="footer-social">
                <h3>Connect With Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 FitTrack. All rights reserved.</p>
        </div>
    </footer>
    
    <script>
    // Toggle password visibility function
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.querySelector(`.toggle-password`);
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
    </script>
</body>
</html>