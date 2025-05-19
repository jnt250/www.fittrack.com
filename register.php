<?php
// Include the database configuration file
require_once 'config.php';

// Define variables and initialize with empty values
$fullname = $email = $username = $password = $confirm_password = $fitness_goal = "";
$fullname_err = $email_err = $username_err = $password_err = $confirm_password_err = $terms_err = "";

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate Full Name
    if (empty(trim($_POST["fullname"]))) {
        $fullname_err = "Please enter your full name.";
    } else {
        $fullname = trim($_POST["fullname"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE email = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            $email_err = "System error checking email. Please try again later.";
        } else {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                $email_err = "Error checking email. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE username = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            $username_err = "System error checking username. Please try again later.";
        } else {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                $username_err = "Error checking username. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }
    
    // Validate fitness goal
    if (isset($_POST["fitness_goal"])) {
        $fitness_goal = $_POST["fitness_goal"];
    } else {
        $fitness_goal = "general_fitness"; // Default value
    }
    
    // Validate terms agreement
    if (!isset($_POST["terms"])) {
        $terms_err = "You must agree to the terms and conditions.";
    }
    
    // Check input errors before inserting into database
    if (empty($fullname_err) && empty($email_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($terms_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (full_name, email, username, password, fitness_goal) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt === false) {
            echo "<div class='alert alert-danger'>Registration failed. Please try again later.</div>";
        } else {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_fullname, $param_email, $param_username, $param_password, $param_fitness_goal);
            
            // Set parameters
            $param_fullname = $fullname;
            $param_email = $email;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_fitness_goal = $fitness_goal;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Get the user ID of the newly created user
                $user_id = mysqli_insert_id($conn);
                
                // Create an empty profile for the user
                $profile_sql = "INSERT INTO user_profiles (user_id) VALUES (?)";
                $profile_stmt = mysqli_prepare($conn, $profile_sql);
                
                if ($profile_stmt !== false) {
                    mysqli_stmt_bind_param($profile_stmt, "i", $user_id);
                    mysqli_stmt_execute($profile_stmt);
                    mysqli_stmt_close($profile_stmt);
                }
                
                // Display success message
               echo "<div class='alert alert-success'>User created successfully!</div>";
                echo "<a href='login.html' class='btn btn-primary'>Continue to login</a>";

            } else {
                echo "<div class='alert alert-danger'>Registration failed. Please try again later.</div>";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    } else {
        // Display validation errors
        if (!empty($fullname_err)) echo "<div class='alert alert-danger'>" . $fullname_err . "</div>";
        if (!empty($email_err)) echo "<div class='alert alert-danger'>" . $email_err . "</div>";
        if (!empty($username_err)) echo "<div class='alert alert-danger'>" . $username_err . "</div>";
        if (!empty($password_err)) echo "<div class='alert alert-danger'>" . $password_err . "</div>";
        if (!empty($confirm_password_err)) echo "<div class='alert alert-danger'>" . $confirm_password_err . "</div>";
        if (!empty($terms_err)) echo "<div class='alert alert-danger'>" . $terms_err . "</div>";
    }
    
    // Close connection
    mysqli_close($conn);
}
?>