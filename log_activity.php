<?php
// Initialize the session
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Define variables
$activity_type = $duration = "";
$activity_type_err = $duration_err = "";

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate activity type
    if (empty(trim($_POST["activity_type"]))) {
        $activity_type_err = "Please select an activity type.";
    } else {
        $activity_type = trim($_POST["activity_type"]);
    }
    
    // Validate duration
    if (empty(trim($_POST["duration"]))) {
        $duration_err = "Please enter the duration.";
    } elseif (!is_numeric($_POST["duration"]) || $_POST["duration"] <= 0) {
        $duration_err = "Please enter a valid duration.";
    } else {
        $duration = trim($_POST["duration"]);
    }
    
    // Check input errors before inserting into database
    if (empty($activity_type_err) && empty($duration_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO workout_logs (user_id, workout_date, workout_type, duration) VALUES (?, CURDATE(), ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isi", $param_user_id, $param_activity_type, $param_duration);
            
            // Set parameters
            $param_user_id = $_SESSION["user_id"];
            $param_activity_type = $activity_type;
            $param_duration = $duration;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect back to dashboard with success message
                header("location: dashboard.php?log=success");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>